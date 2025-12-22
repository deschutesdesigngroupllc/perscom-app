<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Database\Seeders\ShieldSeeder;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant): void {
            Schema::disableForeignKeyConstraints();

            Role::truncate();
            Permission::truncate();

            Artisan::call('tenants:seed', [
                '--tenants' => $tenant->getTenantKey(),
                '--class' => ShieldSeeder::class,
            ]);

            Schema::enableForeignKeyConstraints();
        });
    }
};
