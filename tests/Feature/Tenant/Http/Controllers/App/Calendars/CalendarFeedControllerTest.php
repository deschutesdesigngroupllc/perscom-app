<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Calendars;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use App\Services\CalendarFeedService;
use Tests\Feature\Tenant\TenantTestCase;

class CalendarFeedControllerTest extends TenantTestCase
{
    public function test_it_returns_only_the_calendars_events(): void
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(['view_any_event', 'view_event']);
        $training = Calendar::factory()->createQuietly(['name' => 'Training']);
        $social = Calendar::factory()->createQuietly();
        Event::factory()->createQuietly(['name' => 'Range Day', 'calendar_id' => $training->getKey(), 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);
        Event::factory()->createQuietly(['name' => 'Unit Barbecue', 'calendar_id' => $social->getKey(), 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);

        $response = $this->get(CalendarFeedService::calendarFeedUrl($user, $training))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8');

        $this->assertStringContainsString('X-WR-CALNAME:', $response->getContent());
        $this->assertStringContainsString('Training', $response->getContent());
        $this->assertStringContainsString('Range Day', $response->getContent());
        $this->assertStringNotContainsString('Unit Barbecue', $response->getContent());
    }

    public function test_an_unknown_token_is_not_found(): void
    {
        $calendar = Calendar::factory()->createQuietly();

        $this->get(route('app.calendar-feeds.calendar', ['token' => 'invalid', 'calendar' => $calendar->getKey()]))
            ->assertNotFound();
    }

    public function test_members_who_cannot_view_events_are_refused(): void
    {
        $calendar = Calendar::factory()->createQuietly();

        $this->get(CalendarFeedService::calendarFeedUrl(User::factory()->createQuietly(), $calendar))
            ->assertForbidden();
    }
}
