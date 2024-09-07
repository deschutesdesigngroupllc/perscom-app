<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PassportSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
