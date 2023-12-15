<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('permissions.guards') as $guard) {
            foreach (config('permissions.permissions') as $permission => $description) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guard,
                ], [
                    'description' => $description,
                ]);
            }
        }
    }
}
