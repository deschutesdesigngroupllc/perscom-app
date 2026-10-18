<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Events;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Tests\Feature\Tenant\TenantTestCase;

class CalendarControllerTest extends TenantTestCase
{
    public function test_members_can_download_an_event(): void
    {
        $event = Event::factory()->createQuietly(['name' => 'Range Day', 'all_day' => false, 'repeats' => false, 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);

        $response = $this->actingAs($this->member())
            ->get(route('app.events.calendar', ['event' => $event->getKey()]))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->assertHeader('Content-Disposition', 'attachment; filename="range-day.ics"');

        $this->assertStringContainsString('SUMMARY:Range Day', $response->getContent());
    }

    public function test_a_single_occurrence_of_a_repeating_event_can_be_downloaded(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $start = Date::parse('2026-05-07 20:00:00');
        $event = Event::factory()->createQuietly(['all_day' => false, 'repeats' => true, 'starts' => $start, 'ends' => $start->copy()->addHour()]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 1, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);

        $content = $this->actingAs($this->member())
            ->get(route('app.events.calendar', ['event' => $event->getKey(), 'occurrence' => Date::parse('2026-06-04 20:00:00')->getTimestamp()]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString("DTSTART:20260604T200000Z\r\n", $content);
        $this->assertStringNotContainsString('RRULE', $content);
    }

    public function test_members_without_permission_are_refused(): void
    {
        $event = Event::factory()->createQuietly();

        $this->actingAs(User::factory()->createQuietly())
            ->get(route('app.events.calendar', ['event' => $event->getKey()]))
            ->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $event = Event::factory()->createQuietly();

        $this->get(route('app.events.calendar', ['event' => $event->getKey()]))
            ->assertRedirect();
    }

    private function member(): User
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(['view_any_event', 'view_event']);

        return $user;
    }
}
