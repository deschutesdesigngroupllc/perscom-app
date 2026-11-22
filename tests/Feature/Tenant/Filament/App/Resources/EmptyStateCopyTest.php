<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\IssuerResource\Pages\ListIssuers;
use App\Models\Issuer;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class EmptyStateCopyTest extends TenantTestCase
{
    public function test_empty_tables_explain_what_belongs_there(): void
    {
        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);

        Issuer::query()->delete();

        Livewire::test(ListIssuers::class)
            ->assertSee('No Issuers Yet')
            ->assertSee('Issuers are the organizations that grant credentials.')
            ->assertDontSee('to view. Create one to get started.');
    }
}
