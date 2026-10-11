<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Exceptions\EventCheckInException;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Services\EventAttendanceService;
use Tests\Feature\Tenant\TenantTestCase;

class EventAttendanceServiceTest extends TenantTestCase
{
    public function test_check_in_window_opens_an_hour_before_and_closes_an_hour_after(): void
    {
        $event = $this->makeEvent(startsInMinutes: 120, durationMinutes: 60);

        [$opens, $closes] = EventAttendanceService::checkInWindow($event);

        $this->assertTrue($opens->equalTo($event->starts->copy()->subHour()));
        $this->assertTrue($closes->equalTo($event->ends->copy()->addHour()));
    }

    public function test_is_within_check_in_window(): void
    {
        $event = $this->makeEvent(startsInMinutes: 30, durationMinutes: 60);

        $this->assertTrue(EventAttendanceService::isWithinCheckInWindow($event));
        $this->assertFalse(EventAttendanceService::isWithinCheckInWindow($event, now()->subHours(2)));
        $this->assertFalse(EventAttendanceService::isWithinCheckInWindow($event, now()->addHours(4)));
    }

    public function test_check_in_marks_an_existing_registration_as_attended(): void
    {
        $event = $this->makeEvent(startsInMinutes: 30, durationMinutes: 60);
        $user = User::factory()->createQuietly();
        $event->registrations()->attach($user, ['status' => EventRegistrationStatus::GOING]);

        $registration = EventAttendanceService::checkIn($event, $user);

        $this->assertSame(EventRegistrationStatus::ATTENDED, $registration->status);
        $this->assertNotNull($registration->checked_in_at);
    }

    public function test_check_in_creates_a_registration_when_registration_is_enabled(): void
    {
        $event = $this->makeEvent(startsInMinutes: 30, durationMinutes: 60);
        $user = User::factory()->createQuietly();

        EventAttendanceService::checkIn($event, $user);

        $this->assertDatabaseHas('events_registrations', [
            'event_id' => $event->getKey(),
            'user_id' => $user->getKey(),
            'status' => EventRegistrationStatus::ATTENDED->value,
        ]);
    }

    public function test_check_in_fails_outside_the_window(): void
    {
        $event = $this->makeEvent(startsInMinutes: 300, durationMinutes: 60);

        $this->expectException(EventCheckInException::class);

        EventAttendanceService::checkIn($event, User::factory()->createQuietly());
    }

    public function test_check_in_fails_for_unregistered_user_when_registration_is_disabled(): void
    {
        $event = $this->makeEvent(startsInMinutes: 30, durationMinutes: 60, registrationEnabled: false);

        $this->expectException(EventCheckInException::class);

        EventAttendanceService::checkIn($event, User::factory()->createQuietly());
    }

    public function test_record_attendance_marks_attended_and_no_show(): void
    {
        $event = $this->makeEvent(startsInMinutes: -120, durationMinutes: 60);
        $attendee = User::factory()->createQuietly();
        $absentee = User::factory()->createQuietly();
        $walkIn = User::factory()->createQuietly();
        $event->registrations()->attach([$attendee->getKey(), $absentee->getKey()], ['status' => EventRegistrationStatus::GOING]);

        EventAttendanceService::recordAttendance($event, [$attendee->getKey(), $walkIn->getKey()], [$absentee->getKey()]);

        $this->assertDatabaseHas('events_registrations', ['user_id' => $attendee->getKey(), 'status' => EventRegistrationStatus::ATTENDED->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $walkIn->getKey(), 'status' => EventRegistrationStatus::ATTENDED->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $absentee->getKey(), 'status' => EventRegistrationStatus::NO_SHOW->value]);
    }

    public function test_record_attendance_bypasses_a_closed_registration(): void
    {
        $event = $this->makeEvent(startsInMinutes: -120, durationMinutes: 60, registrationEnabled: false);
        $user = User::factory()->createQuietly();

        EventAttendanceService::recordAttendance($event, [$user->getKey()]);

        $this->assertDatabaseHas('events_registrations', ['user_id' => $user->getKey(), 'status' => EventRegistrationStatus::ATTENDED->value]);
    }

    public function test_attendance_creates_a_single_training_record_when_enabled(): void
    {
        $event = $this->makeEvent(startsInMinutes: -120, durationMinutes: 60, createsTrainingRecord: true);
        $user = User::factory()->createQuietly();

        EventAttendanceService::recordAttendance($event, [$user->getKey()]);
        EventAttendanceService::recordAttendance($event, [], [$user->getKey()]);
        EventAttendanceService::recordAttendance($event, [$user->getKey()]);

        $this->assertSame(1, TrainingRecord::query()->where('user_id', $user->getKey())->where('event_id', $event->getKey())->count());
    }

    public function test_attendance_does_not_create_a_training_record_when_disabled(): void
    {
        $event = $this->makeEvent(startsInMinutes: -120, durationMinutes: 60);
        $user = User::factory()->createQuietly();

        EventAttendanceService::recordAttendance($event, [$user->getKey()]);

        $this->assertDatabaseMissing('records_trainings', ['user_id' => $user->getKey(), 'event_id' => $event->getKey()]);
    }

    private function makeEvent(int $startsInMinutes, int $durationMinutes, bool $registrationEnabled = true, bool $createsTrainingRecord = false): Event
    {
        $starts = now()->addMinutes($startsInMinutes)->startOfMinute();

        return Event::factory()->createQuietly([
            'all_day' => false,
            'starts' => $starts,
            'ends' => $starts->copy()->addMinutes($durationMinutes),
            'registration_enabled' => $registrationEnabled,
            'registration_deadline' => null,
            'creates_training_record' => $createsTrainingRecord,
        ]);
    }
}
