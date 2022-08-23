<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Laraform\Support\Arr;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$allPermissions = collect(config('permissions.permissions'))->keys()->toArray();
	    $defaultPermissions = config('permissions.default');
	    foreach (config('permissions.roles') as $role => $description) {
		    if (!Role::where('name', $role)->first()) {
			    $role = Role::factory()->create([
				    'name' => $role,
				    'description' => $description
			    ]);

			    if (Arr::exists($defaultPermissions, $role->name)) {
				    $role->givePermissionTo(Arr::get($defaultPermissions, $role->name));
			    } else {
				    $role->givePermissionTo($allPermissions);
			    }
		    }
	    }
    }
}
