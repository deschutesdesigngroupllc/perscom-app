<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Pages\Dashboard;
use App\Filament\App\Widgets\OrganizationInfoWidget;
use App\Models\User;
use App\Settings\OnboardingSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Tests\Feature\Tenant\TenantTestCase;

use function Pest\Livewire\livewire;

class OrganizationInfoWidgetTest extends TenantTestCase
{
    public function test_super_admin_sees_resume_link_when_onboarding_is_not_accessible(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => true,
            'completed_at' => null,
        ]);

        $this->actingAsSuperAdmin();

        livewire(OrganizationInfoWidget::class)
            ->assertSee('Resume Setup Wizard');
    }

    public function test_resume_link_is_hidden_while_onboarding_is_still_accessible(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => false,
            'completed_at' => null,
        ]);

        $this->actingAsSuperAdmin();

        livewire(OrganizationInfoWidget::class)
            ->assertSuccessful()
            ->assertDontSee('Resume Setup Wizard');
    }

    public function test_non_super_admin_does_not_see_resume_link(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => true,
            'completed_at' => null,
        ]);

        $this->actingAs(User::factory()->createQuietly());

        livewire(OrganizationInfoWidget::class)
            ->assertDontSee('Resume Setup Wizard');
    }

    public function test_resuming_onboarding_redirects_to_the_dashboard(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => true,
            'completed_at' => null,
        ]);

        $this->actingAsSuperAdmin();

        livewire(OrganizationInfoWidget::class)
            ->call('resumeOnboarding')
            ->assertRedirect(Dashboard::getUrl());
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
