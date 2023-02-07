<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ranks = [
            [
                'name' => 'Private',
                'abbreviation' => 'PVT',
                'paygrade' => 'E-1',
            ],
            [
                'name' => 'Private Second Class',
                'abbreviation' => 'PV2',
                'paygrade' => 'E-2',
            ],
            [
                'name' => 'Private First Class',
                'abbreviation' => 'PFC',
                'paygrade' => 'E-3',
            ],
            [
                'name' => 'Specialist',
                'abbreviation' => 'SPC',
                'paygrade' => 'E-4',
            ],
            [
                'name' => 'Corporal',
                'abbreviation' => 'CPL',
                'paygrade' => 'E-4',
            ],
            [
                'name' => 'Sergeant',
                'abbreviation' => 'SGT',
                'paygrade' => 'E-5',
            ],
            [
                'name' => 'Staff Sergeant',
                'abbreviation' => 'SSG',
                'paygrade' => 'E-6',
            ],
            [
                'name' => 'Sergeant First Class',
                'abbreviation' => 'SFC',
                'paygrade' => 'E-7',
            ],
            [
                'name' => 'Master Sergeant',
                'abbreviation' => 'MSG',
                'paygrade' => 'E-8',
            ],
            [
                'name' => 'First Sergeant',
                'abbreviation' => '1SG',
                'paygrade' => 'E-8',
            ],
            [
                'name' => 'Sergeant Major',
                'abbreviation' => 'SGM',
                'paygrade' => 'E-9',
            ],
            [
                'name' => 'Command Sergeant Major',
                'abbreviation' => 'CSM',
                'paygrade' => 'E-9',
            ],
            [
                'name' => 'Sergeant Major of the Army',
                'abbreviation' => 'SMA',
                'paygrade' => 'E-9S',
            ]
        ];

        foreach (array_reverse($ranks) as $rank) {
            Rank::factory()->state($rank)->create();
        }
    }
}
