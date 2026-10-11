<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\Dashboard;
use App\Models\Group;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use App\Settings\OnboardingSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class DashboardOnboardingTest extends TenantTestCase
{
    public function test_onboarding_action_is_visible_for_super_admin_when_accessible(): void
    {
        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->assertActionVisible('onboarding');
    }

    public function test_onboarding_action_is_hidden_when_not_accessible(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => true,
            'completed_at' => null,
        ]);

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->assertActionHidden('onboarding');
    }

    public function test_dont_show_again_permanently_dismisses_onboarding(): void
    {
        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        (new Dashboard)->dismissOnboarding();

        $settings = resolve(OnboardingSettings::class);

        $this->assertTrue($settings->dismissed);
        $this->assertFalse($settings->isAccessible());
    }

    public function test_completing_onboarding_creates_the_organization_structure(): void
    {
        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->callAction('onboarding', data: [
                'group' => ['name' => 'Operations', 'description' => 'Operational units.'],
                'unit' => ['name' => 'Alpha Company', 'description' => 'First company.'],
                'position' => ['name' => 'Platoon Leader'],
                'rank' => ['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'E-5'],
                'status' => ['name' => 'Active Duty', 'color' => '#16a34a'],
                'finish' => ['dont_show_again' => true],
            ])
            ->assertHasNoActionErrors();

        $unit = Unit::query()->where('name', 'Alpha Company')->firstOrFail();

        $this->assertSame('Operations', $unit->groups()->value('name'));
        $this->assertDatabaseHas(Group::class, ['name' => 'Operations', 'description' => 'Operational units.']);
        $this->assertDatabaseHas(Position::class, ['name' => 'Platoon Leader']);
        $this->assertDatabaseHas(Rank::class, ['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'E-5']);
        $this->assertDatabaseHas(Status::class, ['name' => 'Active Duty', 'color' => '#16a34a']);
        $this->assertTrue(resolve(OnboardingSettings::class)->completed);
    }

    public function test_completing_onboarding_reuses_existing_records(): void
    {
        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        $group = Group::factory()->createQuietly(['name' => 'Operations']);
        $unit = Unit::factory()->createQuietly(['name' => 'Alpha Company']);
        $rank = Rank::factory()->createQuietly(['name' => 'Sergeant']);

        Livewire::test(Dashboard::class)
            ->callAction('onboarding', data: [
                'group' => ['name' => 'operations'],
                'unit' => ['name' => 'alpha company'],
                'rank' => ['name' => 'SERGEANT'],
                'finish' => ['dont_show_again' => false],
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(1, Group::query()->where('name', 'Operations')->count());
        $this->assertSame(1, Unit::query()->where('name', 'Alpha Company')->count());
        $this->assertSame(1, Rank::query()->where('name', 'Sergeant')->count());
        $this->assertTrue($unit->groups()->whereKey($group->getKey())->exists());
        $this->assertSame('Sergeant', $rank->refresh()->name);
        $this->assertTrue(resolve(OnboardingSettings::class)->dismissed);
    }

    private function fakeAccessibleOnboarding(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => false,
            'completed_at' => null,
        ]);
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
