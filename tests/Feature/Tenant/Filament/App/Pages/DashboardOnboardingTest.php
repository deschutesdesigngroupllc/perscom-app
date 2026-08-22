<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\Dashboard;
use App\Models\User;
use App\Settings\OnboardingSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Tests\Feature\Tenant\TenantTestCase;

use function Pest\Livewire\livewire;

class DashboardOnboardingTest extends TenantTestCase
{
    public function test_onboarding_action_is_visible_for_super_admin_when_accessible(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => false,
            'completed_at' => null,
        ]);

        $this->actingAsSuperAdmin();

        livewire(Dashboard::class)
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

        livewire(Dashboard::class)
            ->assertActionHidden('onboarding');
    }

    public function test_dont_show_again_permanently_dismisses_onboarding(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => false,
            'completed_at' => null,
        ]);

        $this->actingAsSuperAdmin();

        (new Dashboard)->dismissOnboarding();

        $settings = resolve(OnboardingSettings::class);

        $this->assertTrue($settings->dismissed);
        $this->assertFalse($settings->isAccessible());
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
