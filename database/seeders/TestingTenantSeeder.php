<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestingTenantSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->state([
            'name' => 'Test User',
            'position_id' => null,
            'rank_id' => null,
            'specialty_id' => null,
            'status_id' => null,
            'unit_id' => null,
        ])->createQuietly();

        $user->assignRole('user');
    }
}
