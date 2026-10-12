<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Exceptions\EventCheckInException;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\EventRegistration;
use App\Models\Schedule;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Services\EventAttendanceService;
use App\Services\EventOccurrenceService;
use Illuminate\Support\Facades\Date;
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

    public function test_check_in_to_a_repeating_event_records_each_occurrence_separately(): void
    {
        $this->travelTo(Date::parse('2026-06-04 20:30:00'));
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'), createsTrainingRecord: true);
        $user = User::factory()->createQuietly();

        EventAttendanceService::checkIn($event, $user);

        $this->travelTo(Date::parse('2026-06-11 20:30:00'));
        EventAttendanceService::checkIn($event, $user);

        $registrations = EventRegistration::query()->where('event_id', $event->getKey())->where('user_id', $user->getKey())->with('occurrence')->get();

        $this->assertSame(['2026-06-04 20:00', '2026-06-11 20:00'], $registrations->map(fn (EventRegistration $registration): string => $registration->occurrence->starts->format('Y-m-d H:i'))->sort()->values()->all());
        $this->assertTrue($registrations->every(fn (EventRegistration $registration): bool => $registration->status === EventRegistrationStatus::ATTENDED));
        $this->assertSame(2, TrainingRecord::query()->where('user_id', $user->getKey())->where('event_id', $event->getKey())->whereNotNull('event_occurrence_id')->count());
        $this->assertFalse($event->registrations()->exists());
    }

    public function test_check_in_fails_when_the_occurrence_is_cancelled(): void
    {
        $this->travelTo(Date::parse('2026-06-11 20:30:00'));
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        EventOccurrence::factory()->for($event)->cancelled()->createQuietly([
            'starts' => Date::parse('2026-06-11 20:00:00'),
            'ends' => Date::parse('2026-06-11 21:00:00'),
        ]);

        $this->assertFalse(EventAttendanceService::isWithinCheckInWindow($event));
        $this->expectException(EventCheckInException::class);

        EventAttendanceService::checkIn($event, User::factory()->createQuietly());
    }

    public function test_an_occurrence_registration_overrides_the_series_registration(): void
    {
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $user = User::factory()->createQuietly();
        $event->registrations()->attach($user, ['status' => EventRegistrationStatus::GOING]);

        $skipped = EventOccurrenceService::find($event, Date::parse('2026-06-11 20:00:00'));
        $attending = EventOccurrenceService::find($event, Date::parse('2026-06-18 20:00:00'));

        EventRegistration::query()->create([
            'event_id' => $event->getKey(),
            'user_id' => $user->getKey(),
            'event_occurrence_id' => EventOccurrenceService::persist($skipped)->getKey(),
            'status' => EventRegistrationStatus::NOT_GOING,
        ]);

        $this->assertSame(EventRegistrationStatus::NOT_GOING, EventAttendanceService::statusFor($skipped, $user));
        $this->assertSame(EventRegistrationStatus::GOING, EventAttendanceService::statusFor($attending, $user));
        $this->assertCount(1, $event->registrations()->get());
    }

    public function test_record_attendance_targets_the_given_occurrence(): void
    {
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $user = User::factory()->createQuietly();
        $occurrence = EventOccurrenceService::find($event, Date::parse('2026-06-11 20:00:00'));

        EventAttendanceService::recordAttendance($event, [], [$user->getKey()], $occurrence);

        $this->assertDatabaseHas('events_registrations', [
            'event_id' => $event->getKey(),
            'user_id' => $user->getKey(),
            'event_occurrence_id' => $occurrence->occurrence?->getKey(),
            'status' => EventRegistrationStatus::NO_SHOW->value,
        ]);
    }

    private function makeWeeklyEvent(mixed $start, bool $createsTrainingRecord = false): Event
    {
        $event = Event::factory()->createQuietly([
            'all_day' => false,
            'repeats' => true,
            'starts' => $start,
            'ends' => $start->copy()->addHour(),
            'registration_enabled' => true,
            'registration_deadline' => null,
            'creates_training_record' => $createsTrainingRecord,
        ]);

        Schedule::factory()->for($event, 'repeatable')->createQuietly([
            'start' => $start,
            'duration' => 1,
            'frequency' => ScheduleFrequency::WEEKLY,
            'interval' => 1,
            'end_type' => ScheduleEndType::NEVER,
        ]);

        return $event->refresh();
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
