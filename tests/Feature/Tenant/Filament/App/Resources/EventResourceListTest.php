<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\EventResource\Pages\ListEvents;
use App\Models\Event;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class EventResourceListTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_upcoming_lists_soonest_first_and_all_lists_newest_first(): void
    {
        $earlier = Event::factory()->createQuietly(['starts' => now()->addDays(2), 'ends' => now()->addDays(2)->addHour(), 'repeats' => false]);
        $later = Event::factory()->createQuietly(['starts' => now()->addDays(9), 'ends' => now()->addDays(9)->addHour(), 'repeats' => false]);

        Livewire::test(ListEvents::class)
            ->assertTableColumnExists('starts')
            ->assertCanSeeTableRecords([$earlier, $later], inOrder: true)
            ->set('activeTab', 'all')
            ->assertCanSeeTableRecords([$later, $earlier], inOrder: true);
    }

    public function test_upcoming_view_hides_past_one_time_events(): void
    {
        $past = Event::factory()->createQuietly(['name' => 'Past Range Day', 'starts' => now()->subWeek(), 'ends' => now()->subWeek()->addHour(), 'repeats' => false]);
        $upcoming = Event::factory()->createQuietly(['name' => 'Upcoming Range Day', 'starts' => now()->addWeek(), 'ends' => now()->addWeek()->addHour(), 'repeats' => false]);

        Livewire::test(ListEvents::class)
            ->set('activeTab', 'upcoming')
            ->assertCanSeeTableRecords([$upcoming])
            ->assertCanNotSeeTableRecords([$past])
            ->set('activeTab', 'past')
            ->assertCanSeeTableRecords([$past])
            ->assertCanNotSeeTableRecords([$upcoming]);
    }

    public function test_attendance_can_be_taken_from_the_row_once_an_event_has_started(): void
    {
        $started = Event::factory()->createQuietly(['starts' => now()->subHour(), 'ends' => now()->addHour(), 'repeats' => false]);
        $future = Event::factory()->createQuietly(['starts' => now()->addDay(), 'ends' => now()->addDay()->addHour(), 'repeats' => false]);

        Livewire::test(ListEvents::class)
            ->assertActionVisible(TestAction::make('takeAttendance')->table($started))
            ->assertActionHidden(TestAction::make('takeAttendance')->table($future));
    }
}
