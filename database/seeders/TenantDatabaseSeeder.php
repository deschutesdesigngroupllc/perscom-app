<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Tenant\SetupTenantAccount;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /** @var SetupTenantAccount $action */
        $action = app(SetupTenantAccount::class);
        $action
            ->shouldCreateUser(false)
            ->shouldCreateAnnouncement(false)
            ->shouldCreatePages(false)
            ->handle(tenant());

        $user = User::factory()->unassigned()->createQuietly([
            'name' => 'Test User',
            'email' => 'test@deschutesdesigngroup.com',
        ]);
        $user->assignRole(Utils::getSuperAdminName());
    }
}
