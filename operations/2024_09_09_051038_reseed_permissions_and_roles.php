<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Database\Seeders\ShieldSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        $tenantIds = Tenant::pluck('id')->toArray();

        if (empty($tenantIds)) {
            return;
        }

        $numChunks = 10;
        $chunkSize = ceil(count($tenantIds) / $numChunks);
        $tenantChunks = array_chunk($tenantIds, (int) $chunkSize);

        $operations = [];
        foreach ($tenantChunks as $tenantIds) {
            $operations[] = fn () => Tenant::whereIn('id', $tenantIds)->get()->each(fn (Tenant $tenant) => $this->reseedTenant($tenant));
        }

        Concurrency::driver('fork')->run($operations);
    }

    protected function reseedTenant(Tenant $tenant): void
    {
        $tenant->run(function (Tenant $tenant): void {
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
