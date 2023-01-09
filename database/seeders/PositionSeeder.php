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
                'order' => 11,
            ],
            [
                'name' => 'Fireteam Leader',
                'order' => 10,
            ],
            [
                'name' => 'Squad Medic',
                'order' => 9,
            ],
            [
                'name' => 'Squad Leader',
                'order' => 8,
            ],
            [
                'name' => 'Forward Observer',
                'order' => 7,
            ],
            [
                'name' => 'Platoon Medic',
                'order' => 6,
            ],
            [
                'name' => 'Platoon Sergeant',
                'order' => 5,
            ],
            [
                'name' => 'Platoon Leader',
                'order' => 4,
            ],
            [
                'name' => 'Company First Sergeant',
                'order' => 3,
            ],
            [
                'name' => 'Company Executive Officer',
                'order' => 2,
            ],
            [
                'name' => 'Company Commander',
                'order' => 1,
            ],
        ];

        foreach ($positions as $position) {
            Position::factory()->state($position)->create();
        }
    }
}
