<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Events;

use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\User;
use App\Services\EventAttendanceService;
use Tests\Feature\Tenant\TenantTestCase;

class CheckInControllerTest extends TenantTestCase
{
    public function test_signed_link_checks_the_user_in(): void
    {
        $event = $this->makeEvent(startsInMinutes: 30);
        $user = User::factory()->createQuietly();

        $this->actingAs($user)
            ->get(EventAttendanceService::checkInUrl($event))
            ->assertRedirect();

        $this->assertDatabaseHas('events_registrations', [
            'event_id' => $event->getKey(),
            'user_id' => $user->getKey(),
            'status' => EventRegistrationStatus::ATTENDED->value,
        ]);
    }

    public function test_unsigned_link_is_rejected(): void
    {
        $event = $this->makeEvent(startsInMinutes: 30);

        $this->actingAs(User::factory()->createQuietly())
            ->get(route('app.events.check-in', ['event' => $event->getKey()]))
            ->assertForbidden();
    }

    public function test_tampered_link_is_rejected(): void
    {
        $event = $this->makeEvent(startsInMinutes: 30);
        $other = $this->makeEvent(startsInMinutes: 30);

        $url = str_replace(
            sprintf('events/%s/', $event->getKey()),
            sprintf('events/%s/', $other->getKey()),
            EventAttendanceService::checkInUrl($event),
        );

        $this->actingAs(User::factory()->createQuietly())
            ->get($url)
            ->assertForbidden();
    }

    public function test_check_in_outside_window_does_not_record_attendance(): void
    {
        $event = $this->makeEvent(startsInMinutes: 600);
        $user = User::factory()->createQuietly();

        $this->actingAs($user)
            ->get(EventAttendanceService::checkInUrl($event))
            ->assertRedirect();

        $this->assertDatabaseMissing('events_registrations', [
            'event_id' => $event->getKey(),
            'user_id' => $user->getKey(),
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $event = $this->makeEvent(startsInMinutes: 30);

        $this->get(EventAttendanceService::checkInUrl($event))
            ->assertRedirect();

        $this->assertDatabaseMissing('events_registrations', [
            'event_id' => $event->getKey(),
        ]);
    }

    private function makeEvent(int $startsInMinutes): Event
    {
        $starts = now()->addMinutes($startsInMinutes)->startOfMinute();

        return Event::factory()->createQuietly([
            'all_day' => false,
            'starts' => $starts,
            'ends' => $starts->copy()->addHour(),
            'registration_enabled' => true,
            'registration_deadline' => null,
        ]);
    }
}
