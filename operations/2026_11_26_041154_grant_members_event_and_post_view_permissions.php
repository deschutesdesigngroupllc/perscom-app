<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\PermissionRegistrar;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

/**
 * Grant members the view permissions for events, news, and announcements.
 */
return new class extends OneTimeOperation
{
    /**
     * @var array<int, string>
     */
    protected array $permissions = ['view_event', 'view_newsfeed', 'view_announcement'];

    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant): void {
            $role = Role::query()->where('name', 'User')->where('guard_name', 'web')->first();

            if (! $role instanceof Role) {
                return;
            }

            $role->givePermissionTo(collect($this->permissions)
                ->map(fn (string $name): PermissionContract => Permission::findOrCreate($name, 'web'))
                ->all());

            resolve(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    }
};
