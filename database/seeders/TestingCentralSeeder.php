<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class TestingCentralSeeder extends Seeder
{
    public function run(): void
    {
        Admin::factory()->state([
            'name' => 'Test Admin',
        ])->create();
    }
}
