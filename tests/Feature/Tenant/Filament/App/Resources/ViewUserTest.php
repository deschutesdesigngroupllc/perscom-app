<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Models\Credential;
use App\Models\Enums\CredentialType;
use App\Models\TrainingRecord;
use App\Models\User;
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

    protected function actingAsSuperAdmin(): void
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);
    }
}
