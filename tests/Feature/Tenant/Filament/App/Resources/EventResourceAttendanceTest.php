<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\Permission;
use App\Models\Schedule;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class EventResourceAttendanceTest extends TenantTestCase
{
    public function test_take_attendance_marks_attended_no_show_and_walk_ins(): void
    {
        $this->actingAsSuperAdmin();

        $event = Event::factory()->createQuietly(['registration_enabled' => true, 'registration_deadline' => null]);
        $attendee = User::factory()->createQuietly();
        $absentee = User::factory()->createQuietly();
        $walkIn = User::factory()->createQuietly();
        $event->registrations()->attach([$attendee->getKey(), $absentee->getKey()], ['status' => EventRegistrationStatus::GOING]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->callAction('takeAttendance', [
                'attended' => [$attendee->getKey()],
                'additional' => [$walkIn->getKey()],
                'mark_no_show' => true,
            ])
            ->assertHasNoActionErrors()
            ->assertNotified('Attendance has been saved.');

        $this->assertDatabaseHas('events_registrations', ['user_id' => $attendee->getKey(), 'status' => EventRegistrationStatus::ATTENDED->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $walkIn->getKey(), 'status' => EventRegistrationStatus::ATTENDED->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $absentee->getKey(), 'status' => EventRegistrationStatus::NO_SHOW->value]);
    }

    public function test_take_attendance_records_the_selected_occurrence_of_a_repeating_event(): void
    {
        $this->travelTo(Date::parse('2026-06-20 12:00:00'));
        $this->actingAsSuperAdmin();

        $start = Date::parse('2026-06-04 20:00:00');
        $event = Event::factory()->createQuietly(['repeats' => true, 'all_day' => false, 'starts' => $start, 'ends' => $start->copy()->addHour(), 'registration_enabled' => true, 'registration_deadline' => null]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 1, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);
        $attendee = User::factory()->createQuietly();
        $absentee = User::factory()->createQuietly();
        $event->registrations()->attach([$attendee->getKey(), $absentee->getKey()], ['status' => EventRegistrationStatus::GOING]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->callAction('takeAttendance', [
                'occurrence' => Date::parse('2026-06-11 20:00:00')->toIso8601String(),
                'attended' => [$attendee->getKey()],
                'mark_no_show' => true,
            ])
            ->assertHasNoActionErrors();

        $occurrence = EventOccurrence::query()->where('event_id', $event->getKey())->sole();

        $this->assertSame('2026-06-11 20:00', $occurrence->starts->format('Y-m-d H:i'));
        $this->assertDatabaseHas('events_registrations', ['user_id' => $attendee->getKey(), 'event_occurrence_id' => $occurrence->getKey(), 'status' => EventRegistrationStatus::ATTENDED->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $absentee->getKey(), 'event_occurrence_id' => $occurrence->getKey(), 'status' => EventRegistrationStatus::NO_SHOW->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $attendee->getKey(), 'event_occurrence_id' => null, 'status' => EventRegistrationStatus::GOING->value]);
    }

    public function test_take_attendance_leaves_unchecked_registrants_when_no_show_is_disabled(): void
    {
        $this->actingAsSuperAdmin();

        $event = Event::factory()->createQuietly(['registration_enabled' => true, 'registration_deadline' => null]);
        $attendee = User::factory()->createQuietly();
        $absentee = User::factory()->createQuietly();
        $event->registrations()->attach([$attendee->getKey(), $absentee->getKey()], ['status' => EventRegistrationStatus::GOING]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->callAction('takeAttendance', [
                'attended' => [$attendee->getKey()],
                'mark_no_show' => false,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('events_registrations', ['user_id' => $attendee->getKey(), 'status' => EventRegistrationStatus::ATTENDED->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $absentee->getKey(), 'status' => EventRegistrationStatus::GOING->value]);
    }

    public function test_take_attendance_only_marks_unchecked_going_registrants_as_no_show(): void
    {
        $this->actingAsSuperAdmin();

        $event = Event::factory()->createQuietly(['registration_enabled' => true, 'registration_deadline' => null]);
        $going = User::factory()->createQuietly();
        $maybe = User::factory()->createQuietly();
        $notGoing = User::factory()->createQuietly();
        $event->registrations()->attach($going->getKey(), ['status' => EventRegistrationStatus::GOING]);
        $event->registrations()->attach($maybe->getKey(), ['status' => EventRegistrationStatus::TENTATIVE]);
        $event->registrations()->attach($notGoing->getKey(), ['status' => EventRegistrationStatus::NOT_GOING]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->callAction('takeAttendance', [
                'attended' => [],
                'mark_no_show' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('events_registrations', ['user_id' => $going->getKey(), 'status' => EventRegistrationStatus::NO_SHOW->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $maybe->getKey(), 'status' => EventRegistrationStatus::TENTATIVE->value]);
        $this->assertDatabaseHas('events_registrations', ['user_id' => $notGoing->getKey(), 'status' => EventRegistrationStatus::NOT_GOING->value]);
    }

    public function test_take_attendance_defaults_to_the_most_recent_occurrence_that_has_started(): void
    {
        $this->travelTo(Date::parse('2026-06-24 12:00:00'));
        $this->actingAsSuperAdmin();

        $start = Date::parse('2026-06-04 20:00:00');
        $event = Event::factory()->createQuietly(['repeats' => true, 'all_day' => false, 'starts' => $start, 'ends' => $start->copy()->addHour(), 'registration_enabled' => true, 'registration_deadline' => null]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 1, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->mountAction('takeAttendance')
            ->assertActionDataSet(['occurrence' => Date::parse('2026-06-18 20:00:00')->toIso8601String()]);
    }

    public function test_check_in_link_action_is_visible_to_admins(): void
    {
        $this->actingAsSuperAdmin();

        $event = Event::factory()->createQuietly();

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertActionVisible('checkInLink')
            ->mountAction('checkInLink')
            ->assertActionMounted('checkInLink');
    }

    public function test_attendance_actions_are_hidden_from_members_without_permission(): void
    {
        $member = User::factory()->createQuietly();
        $member->givePermissionTo(Permission::findOrCreate('view_event'), Permission::findOrCreate('view_any_event'));

        $this->actingAs($member);

        $event = Event::factory()->createQuietly();

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertActionHidden('takeAttendance')
            ->assertActionHidden('checkInLink');
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
