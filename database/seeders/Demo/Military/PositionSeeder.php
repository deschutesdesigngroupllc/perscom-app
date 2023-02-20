<?php

namespace Database\Seeders\Demo\Military;

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
                'name' => 'Detachment Commander',
            ],
            [
                'name' => 'Assistant Detachment Commander',
            ],
            [
                'name' => 'Operations Sergeant',
            ],
            [
                'name' => 'Assistant Operations and Intelligence Sergeant',
            ],
            [
                'name' => 'Weapons Sergeant',
            ],
            [
                'name' => 'Communications Sergeant',
            ],
            [
                'name' => 'Medical Sergeant',
            ],
            [
                'name' => 'Engineering Sergeant',
            ],
        ];

        foreach ($positions as $position) {
            Position::factory()->state($position)->create();
        }
    }
}
