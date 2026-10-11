<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Widgets\MyServiceWidget;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\Rank;
use App\Models\RankRequirement;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class MyServiceWidgetTest extends TenantTestCase
{
    public function test_it_shows_the_members_service_summary(): void
    {
        $unit = Unit::factory()->createQuietly(['name' => 'Bravo Company']);
        $user = User::factory()->createQuietly(['unit_id' => $unit->getKey()]);
        $this->actingAs($user);

        Livewire::test(MyServiceWidget::class)
            ->assertOk()
            ->assertSee('My Service')
            ->assertSee('Service Summary')
            ->assertSee('Bravo Company')
            ->assertSee($user->rank->name)
            ->assertSee('Time In Grade');
    }

    public function test_it_shows_the_members_awards_in_the_ribbon_rack(): void
    {
        $user = User::factory()->createQuietly();
        $this->actingAs($user);

        $award = Award::factory()->createQuietly(['name' => 'Bronze Star']);
        AwardRecord::factory()->createQuietly(['user_id' => $user->getKey(), 'award_id' => $award->getKey(), 'document_id' => null]);

        Livewire::test(MyServiceWidget::class)
            ->assertSee('Ribbon Rack')
            ->assertSee('Bronze Star');
    }

    public function test_it_offers_check_in_for_events_whose_window_is_open(): void
    {
        $user = User::factory()->createQuietly();
        $this->actingAs($user);

        $event = Event::factory()->createQuietly([
            'name' => 'Weekly Drill',
            'all_day' => false,
            'repeats' => false,
            'registration_enabled' => true,
            'starts' => now()->subMinutes(10),
            'ends' => now()->addHour(),
        ]);
        $event->registrations()->attach($user, ['status' => EventRegistrationStatus::GOING->value]);

        Livewire::test(MyServiceWidget::class)
            ->assertSee('Weekly Drill')
            ->assertSee(EventRegistrationStatus::GOING->getLabel())
            ->assertSee('Check In');
    }

    public function test_it_does_not_offer_check_in_before_the_window_opens(): void
    {
        $user = User::factory()->createQuietly();
        $this->actingAs($user);

        Event::factory()->createQuietly([
            'name' => 'Next Month Exercise',
            'all_day' => false,
            'repeats' => false,
            'starts' => now()->addDays(10),
            'ends' => now()->addDays(10)->addHour(),
        ]);

        Livewire::test(MyServiceWidget::class)
            ->assertSee('Next Month Exercise')
            ->assertSee('Not Registered')
            ->assertDontSee('Check In');
    }

    public function test_it_shows_progress_toward_the_next_promotion(): void
    {
        $senior = Rank::factory()->createQuietly(['name' => 'Sergeant', 'order' => 1]);
        $junior = Rank::factory()->createQuietly(['name' => 'Corporal', 'order' => 2]);
        RankRequirement::factory()->createQuietly(['rank_id' => $senior->getKey(), 'min_days_in_service' => 365]);

        $user = User::factory()->createQuietly(['rank_id' => $junior->getKey()]);
        $this->actingAs($user);

        Livewire::test(MyServiceWidget::class)
            ->assertSee('Next Promotion')
            ->assertSee('Progress toward')
            ->assertSee('Sergeant')
            ->assertSee('0 of 1 met');
    }
}
