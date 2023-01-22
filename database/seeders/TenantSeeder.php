<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PassportSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
