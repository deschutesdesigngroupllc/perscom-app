<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = collect(config('permissions.permissions'))->keys()->values();
        $defaultPermissions = collect(config('permissions.default'));
        foreach (config('permissions.guards') as $guard) {
            foreach (config('permissions.roles') as $roleName => $roleDescription) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guard,
                ], [
                    'description' => $roleDescription,
                ]);

                $existingPermissions = $role->permissions->pluck('name');
                if ($defaultPermissions->has($role->name)) {
                    $defaultPermissionsForRole = collect($defaultPermissions->get($role->name));
                    $newPermissions = $defaultPermissionsForRole->diff($existingPermissions);
                } else {
                    $newPermissions = $allPermissions->diff($existingPermissions);
                }

                $role->givePermissionTo($newPermissions->toArray());
            }
        }

    }
}
