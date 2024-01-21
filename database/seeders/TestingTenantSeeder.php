<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestingTenantSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->state([
            'name' => 'Test User'
        ])->createQuietly();
    }
}
