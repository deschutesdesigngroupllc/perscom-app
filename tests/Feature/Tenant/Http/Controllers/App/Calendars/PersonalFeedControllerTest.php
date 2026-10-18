<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Calendars;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use App\Services\CalendarFeedService;
use Tests\Feature\Tenant\TenantTestCase;

class PersonalFeedControllerTest extends TenantTestCase
{
    public function test_a_valid_token_returns_the_members_feed_without_a_session(): void
    {
        $user = $this->member();
        Event::factory()->createQuietly(['name' => 'Range Day', 'all_day' => false, 'repeats' => false, 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);
        Event::factory()->createQuietly(['name' => 'Ancient History', 'all_day' => false, 'repeats' => false, 'starts' => now()->subYear(), 'ends' => now()->subYear()->addHour()]);

        $response = $this->get(CalendarFeedService::personalFeedUrl($user))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8');

        $this->assertStringContainsString('BEGIN:VCALENDAR', $response->getContent());
        $this->assertStringContainsString('SUMMARY:Range Day', $response->getContent());
        $this->assertStringNotContainsString('Ancient History', $response->getContent());
    }

    public function test_an_unknown_token_is_not_found(): void
    {
        $this->get(route('app.calendar-feeds.personal', ['token' => str_repeat('x', 40)]))
            ->assertNotFound();
    }

    public function test_resetting_the_token_invalidates_the_old_link(): void
    {
        $user = $this->member();
        $url = CalendarFeedService::personalFeedUrl($user);

        CalendarFeedService::resetToken($user);

        $this->get($url)->assertNotFound();
        $this->get(CalendarFeedService::personalFeedUrl($user))->assertOk();
    }

    public function test_members_who_cannot_view_events_are_refused(): void
    {
        $user = User::factory()->createQuietly();

        $this->get(CalendarFeedService::personalFeedUrl($user))
            ->assertForbidden();
    }

    public function test_the_feed_can_be_limited_to_calendars(): void
    {
        $user = $this->member();
        $training = Calendar::factory()->createQuietly();
        $social = Calendar::factory()->createQuietly();
        Event::factory()->createQuietly(['name' => 'Range Day', 'calendar_id' => $training->getKey(), 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);
        Event::factory()->createQuietly(['name' => 'Unit Barbecue', 'calendar_id' => $social->getKey(), 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);

        $content = $this->get(CalendarFeedService::personalFeedUrl($user, [$training->getKey()]))->assertOk()->getContent();

        $this->assertStringContainsString('Range Day', $content);
        $this->assertStringNotContainsString('Unit Barbecue', $content);
    }

    public function test_array_calendar_input_is_handled_gracefully(): void
    {
        $user = $this->member();
        $training = Calendar::factory()->createQuietly();
        $social = Calendar::factory()->createQuietly();
        Event::factory()->createQuietly(['name' => 'Range Day', 'calendar_id' => $training->getKey(), 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);
        Event::factory()->createQuietly(['name' => 'Unit Barbecue', 'calendar_id' => $social->getKey(), 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);

        $url = CalendarFeedService::personalFeedUrl($user);

        $this->get($url.'?calendars[]=')->assertOk();

        $content = $this->get($url.'?calendars[]='.$training->getKey().'&calendars[][]=1')->assertOk()->getContent();

        $this->assertStringContainsString('Range Day', $content);
        $this->assertStringNotContainsString('Unit Barbecue', $content);
    }

    private function member(): User
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(['view_any_event', 'view_event']);

        return $user;
    }
}
