<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages\Auth;

use App\Filament\App\Pages\Auth\EditProfile;
use App\Models\User;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class EditProfileTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('app');
    }

    public function test_members_can_hide_their_public_profile(): void
    {
        DashboardSettings::fake(['public_profiles_enabled' => true]);
        SettingsService::flush(DashboardSettings::class);

        $member = User::factory()->createQuietly();
        $this->actingAs($member);

        Livewire::test(EditProfile::class)
            ->assertFormFieldVisible('hide_public_profile')
            ->fillForm(['hide_public_profile' => true, 'timezone' => 'UTC'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($member->refresh()->hide_public_profile);
    }

    public function test_hide_toggle_is_hidden_when_public_profiles_are_off(): void
    {
        DashboardSettings::fake(['public_profiles_enabled' => false]);
        SettingsService::flush(DashboardSettings::class);

        $this->actingAs(User::factory()->createQuietly());

        Livewire::test(EditProfile::class)
            ->assertFormFieldHidden('hide_public_profile');
    }
}
