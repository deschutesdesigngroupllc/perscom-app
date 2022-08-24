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
	    foreach (config('permissions.roles') as $role => $description) {
	    	$role = Role::where('name', $role)->first();
		    if (!$role) {
			    $role = Role::factory()->create([
				    'name' => $role,
				    'description' => $description
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
