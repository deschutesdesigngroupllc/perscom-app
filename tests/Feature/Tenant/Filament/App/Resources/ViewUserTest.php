<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Models\Credential;
use App\Models\Enums\CredentialType;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class ViewUserTest extends TenantTestCase
{
    public function test_it_renders_credentials_of_every_type(): void
    {
        $this->actingAsSuperAdmin();

        $member = User::factory()->createQuietly();
        $record = TrainingRecord::factory()->createQuietly(['user_id' => $member->getKey()]);

        collect(CredentialType::cases())->each(fn (CredentialType $type) => $record->credentials()->attach(
            Credential::factory()->createQuietly(['name' => $type->getLabel().' Credential', 'type' => $type]),
        ));

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->assertOk()
            ->assertSee('Certification Credential')
            ->assertSee('License Credential')
            ->assertSee('Other Credential');
    }

    public function test_public_profile_action_is_only_visible_when_the_profile_is_public(): void
    {
        $this->actingAsSuperAdmin();

        $member = User::factory()->createQuietly(['approved' => true]);

        $this->setPublicProfiles(false);

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->assertActionHidden('publicProfile');

        $this->setPublicProfiles(true);

        Livewire::test(ViewUser::class, ['record' => $member->getKey()])
            ->assertActionVisible('publicProfile');
    }

    protected function actingAsSuperAdmin(): void
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);
    }

    protected function setPublicProfiles(bool $enabled): void
    {
        DashboardSettings::fake(['public_profiles_enabled' => $enabled, 'user_hidden_fields' => []]);

        SettingsService::flush(DashboardSettings::class);
    }
}
