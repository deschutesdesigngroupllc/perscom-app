<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Widgets\UserProfileWidget;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\Rank;
use App\Models\RankRequirement;
use App\Models\Unit;
use App\Models\User;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class UserProfileWidgetTest extends TenantTestCase
{
    public function test_it_shows_the_members_service_summary(): void
    {
        $unit = Unit::factory()->createQuietly(['name' => 'Bravo Company']);
        $user = User::factory()->createQuietly(['unit_id' => $unit->getKey()]);
        $this->actingAs($user);

        Livewire::test(UserProfileWidget::class)
            ->assertOk()
            ->assertSee('Current Rank')
            ->assertSee('Current Assignment')
            ->assertSee('Bravo Company')
            ->assertSee($user->rank->name);
    }

    public function test_it_shows_the_members_awards_in_the_ribbon_rack(): void
    {
        $user = User::factory()->createQuietly();
        $this->actingAs($user);

        $award = Award::factory()->createQuietly(['name' => 'Bronze Star']);
        AwardRecord::factory()->createQuietly(['user_id' => $user->getKey(), 'award_id' => $award->getKey(), 'document_id' => null]);

        Livewire::test(UserProfileWidget::class)
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

        Livewire::test(UserProfileWidget::class)
            ->assertSee('Weekly Drill')
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

        Livewire::test(UserProfileWidget::class)
            ->assertSee('Next Month Exercise')
            ->assertDontSee('Check In');
    }

    public function test_it_shows_progress_toward_the_next_promotion(): void
    {
        $senior = Rank::factory()->createQuietly(['name' => 'Sergeant', 'order' => 1]);
        $junior = Rank::factory()->createQuietly(['name' => 'Corporal', 'order' => 2]);
        RankRequirement::factory()->createQuietly(['rank_id' => $senior->getKey(), 'min_days_in_service' => 365]);

        $user = User::factory()->createQuietly(['rank_id' => $junior->getKey()]);
        $this->actingAs($user);

        Livewire::test(UserProfileWidget::class)
            ->assertSee('Next Promotion')
            ->assertSee('Progress toward')
            ->assertSee('Sergeant')
            ->assertSee('0 of 1 met');
    }

    public function test_it_shows_the_registration_status_when_check_in_is_not_open(): void
    {
        $user = User::factory()->createQuietly();
        $this->actingAs($user);

        $event = Event::factory()->createQuietly([
            'name' => 'Field Exercise',
            'all_day' => false,
            'repeats' => false,
            'starts' => now()->addDays(3),
            'ends' => now()->addDays(3)->addHours(2),
        ]);
        $event->registrations()->attach($user, ['status' => EventRegistrationStatus::GOING->value]);

        Livewire::test(UserProfileWidget::class)
            ->assertSee('Field Exercise')
            ->assertSee(EventRegistrationStatus::GOING->getLabel());
    }

    public function test_record_links_follow_the_profile_tabs_the_organization_shows(): void
    {
        $this->hideProfileRecords(['assignment_records', 'combat_records']);

        $user = User::factory()->createQuietly();
        $this->actingAs($user);

        Livewire::test(UserProfileWidget::class)
            ->assertSee('View Award Records')
            ->assertSee(sprintf('users/%d?relation=0', $user->getKey()), false)
            ->assertSee('View Rank Records')
            ->assertSee(sprintf('users/%d?relation=2', $user->getKey()), false)
            ->assertDontSee('View Assignment Records');
    }

    public function test_the_ribbon_rack_is_hidden_when_award_records_are_hidden(): void
    {
        $this->hideProfileRecords(['award_records']);

        $this->actingAs(User::factory()->createQuietly());

        Livewire::test(UserProfileWidget::class)
            ->assertDontSee('Ribbon Rack');
    }

    /**
     * @param  array<int, string>  $records
     */
    private function hideProfileRecords(array $records): void
    {
        $settings = resolve(DashboardSettings::class);
        $settings->user_hidden_fields = $records;
        $settings->save();

        SettingsService::flush(DashboardSettings::class);
    }
}
