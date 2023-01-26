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
        $existingPermissions = Permission::query()->get()->pluck('description', 'name');
        foreach (config('permissions.guards') as $guard) {
            foreach (config('permissions.permissions') as $permission => $description) {
                if (!$existingPermissions->has($permission)) {
                    Permission::factory()->createQuietly([
                        'name' => $permission,
                        'description' => $description,
                        'guard_name' => $guard,
                    ]);
                }
            }
        }
    }
}
