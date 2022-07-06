<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	foreach (config('permissions.permissions') as $permission => $description) {
        	Permission::factory()->create([
        		'name' => $permission,
		        'description' => $description
	        ]);
        }
    }
}
