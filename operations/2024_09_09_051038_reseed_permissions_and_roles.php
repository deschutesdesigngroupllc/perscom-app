<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Database\Seeders\ShieldSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant) {
            $adminIds = User::role(Utils::getSuperAdminName())->get()->pluck('id');

            Schema::disableForeignKeyConstraints();

            DB::table('roles')->truncate();
            DB::table('permissions')->truncate();
            DB::table('model_has_permissions')->truncate();
            DB::table('model_has_roles')->truncate();
            DB::table('role_has_permissions')->truncate();

            Artisan::call('tenants:seed', [
                '--tenants' => $tenant->getKey(),
                '--class' => ShieldSeeder::class,
            ]);

            Schema::enableForeignKeyConstraints();

            User::findMany($adminIds)->each(fn (User $user) => $user->assignRole(Utils::getSuperAdminName()));
        });
    }
};
