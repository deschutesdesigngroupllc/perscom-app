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
	    foreach (config('permissions.roles') as $role => $description) {
		    $role = Role::factory()->create([
			    'name' => $role,
			    'description' => $description
		    ]);
		    $role->givePermissionTo(collect(config('permissions.permissions'))->keys()->toArray());
	    }
    }
}
