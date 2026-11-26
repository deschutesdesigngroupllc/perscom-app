<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Members can open event, news and announcement details from the calendar and dashboard without the
 * view_any permissions that unlock the admin index pages.
 */
return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    protected array $permissions = ['view_event', 'view_newsfeed', 'view_announcement'];

    public function up(): void
    {
        $role = Role::query()->where('name', 'User')->where('guard_name', 'web')->first();

        if (! $role instanceof Role) {
            return;
        }

        $role->givePermissionTo(collect($this->permissions)
            ->map(fn (string $name): PermissionContract => Permission::findOrCreate($name, 'web'))
            ->all());

        resolve(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $role = Role::query()->where('name', 'User')->where('guard_name', 'web')->first();

        if (! $role instanceof Role) {
            return;
        }

        collect($this->permissions)
            ->filter(fn (string $name): bool => Permission::query()->where('name', $name)->where('guard_name', 'web')->exists())
            ->each(fn (string $name) => $role->revokePermissionTo($name));

        resolve(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
