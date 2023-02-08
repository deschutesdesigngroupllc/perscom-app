<?php

namespace Database\Seeders\Demo\Military;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            [
                'name' => '5th Special Forces Group',
            ],
            [
                'name' => 'Headquarters and Headquarters Company, 5th SFG',
            ],
            [
                'name' => 'Group Support Company, 5th SFG',
            ],
            [
                'name' => '1st Battalion, 5th SFG',
            ],
            [
                'name' => 'Alpha Company, 1st Btn, 5th SFG',
            ],
            [
                'name' => 'ODB 5110, A Co, 1st Btn, 5th SFG',
            ],
            [
                'name' => 'ODA 5111, A Co, 1st Btn, 5th SFG',
            ],
            [
                'name' => 'ODA 5112, A Co, 1st Btn, 5th SFG',
            ],
        ];

        foreach ($units as $unit) {
            Unit::factory()->state($unit)->create();
        }
    }
}
