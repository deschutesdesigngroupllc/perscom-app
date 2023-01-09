<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allPermissions = collect(config('permissions.permissions'))->keys()->values();
        $defaultPermissions = collect(config('permissions.default'));
        foreach (config('permissions.roles') as $roleName => $roleDescription) {
            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                $role = Role::factory()->create([
                    'name' => $roleName,
                    'description' => $roleDescription,
                ]);
            }

            $existingPermissions = $role->permissions->map(function ($permission) {
                return $permission->name;
            })->values();

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
