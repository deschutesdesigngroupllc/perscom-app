<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $positions = [
            [
                'name' => 'Fireteam Member',
            ],
            [
                'name' => 'Fireteam Leader',
            ],
            [
                'name' => 'Squad Medic',
            ],
            [
                'name' => 'Squad Leader',
            ],
            [
                'name' => 'Forward Observer',
            ],
            [
                'name' => 'Platoon Medic',
            ],
            [
                'name' => 'Platoon Sergeant',
            ],
            [
                'name' => 'Platoon Leader',
            ],
            [
                'name' => 'Company First Sergeant',
            ],
            [
                'name' => 'Company Executive Officer',
            ],
            [
                'name' => 'Company Commander',
            ],
        ];

        foreach (array_reverse($positions) as $position) {
            Position::factory()->state($position)->createQuietly();
        }
    }
}
