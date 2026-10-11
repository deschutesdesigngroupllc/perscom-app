<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\Permission;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
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
