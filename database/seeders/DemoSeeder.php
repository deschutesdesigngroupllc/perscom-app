<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->unassigned()->createQuietly([
            'name' => 'Demo User',
            'email' => 'demo@perscom.io',
        ]);
        $user->assignRole(Utils::getSuperAdminName());
    }
}
