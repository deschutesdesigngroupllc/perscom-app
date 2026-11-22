<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Widgets\LaunchPlanWidget;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use App\Services\UserSettingsService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class LaunchPlanWidgetTest extends TenantTestCase
{
    public function test_steps_reflect_real_progress(): void
    {
        $owner = $this->actingAsOwner(['name' => 'Admin', 'rank_id' => null, 'unit_id' => null]);

        $steps = collect((new LaunchPlanWidget)->getSteps())->keyBy('key');

        $this->assertFalse($steps['profile']['done']);

        $rank = Rank::factory()->createQuietly();
        $unit = Unit::factory()->createQuietly();
        Position::factory()->createQuietly();
        $owner->forceFill(['name' => 'Jordan Reyes', 'rank_id' => $rank->getKey(), 'unit_id' => $unit->getKey()])->saveQuietly();

        $steps = collect((new LaunchPlanWidget)->getSteps())->keyBy('key');

        $this->assertTrue($steps['structure']['done']);
        $this->assertTrue($steps['profile']['done']);
    }

    public function test_it_renders_progress_and_calls_to_action_for_the_owner(): void
    {
        $this->actingAsOwner(['name' => 'Admin']);

        Livewire::test(LaunchPlanWidget::class)
            ->assertSee('Launch Plan')
            ->assertSee('Complete your profile')
            ->assertSee('Edit Your Profile');
    }

    public function test_hiding_it_keeps_it_hidden_for_that_owner(): void
    {
        $this->actingAsOwner(['name' => 'Admin']);

        $this->assertTrue(LaunchPlanWidget::canView());

        Livewire::test(LaunchPlanWidget::class)->call('dismiss');

        UserSettingsService::flush();

        $this->assertFalse(LaunchPlanWidget::canView());
    }

    public function test_members_do_not_see_it(): void
    {
        $this->actingAs(User::factory()->withoutRoles()->createQuietly());

        $this->assertFalse(LaunchPlanWidget::canView());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function actingAsOwner(array $attributes = []): User
    {
        $user = User::factory()->createQuietly($attributes);
        $user->assignRole(Utils::getSuperAdminName());
        $this->actingAs($user);

        return $user;
    }
}
