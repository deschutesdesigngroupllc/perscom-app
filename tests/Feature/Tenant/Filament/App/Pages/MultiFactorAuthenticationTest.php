<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Clusters\Settings\Pages\Permission;
use App\Filament\App\Pages\Auth\EditProfile;
use App\Models\User;
use App\Settings\PermissionSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class MultiFactorAuthenticationTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('app');
    }

    public function test_profile_page_renders_multi_factor_authentication_section(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        Livewire::test(EditProfile::class)
            ->assertSuccessful()
            ->assertSee('Authenticator app')
            ->assertSee('Email verification codes');
    }

    public function test_super_admin_can_require_multi_factor_authentication(): void
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        Livewire::test(Permission::class)
            ->fillForm(['require_multi_factor_authentication' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = resolve(PermissionSettings::class);
        $settings->refresh();

        $this->assertTrue($settings->require_multi_factor_authentication);
    }
}
