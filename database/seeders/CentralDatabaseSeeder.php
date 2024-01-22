<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CentralDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Admin::factory()->create([
            'name' => 'Admin',
            'email' => 'test@deschutesdesigngroup.com',
        ]);

        Tenant::factory()->create([
            'id' => 1,
            'name' => 'Test Tenant',
            'email' => 'test@deschutesdesigngroup.com',
            'tenancy_db_name' => 'tenant_local',
        ]);

        Domain::factory()->create([
            'domain' => 'test',
            'tenant_id' => 1,
        ]);
    }
}
