<?php

namespace Database\Seeders\Demo\Military;

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
        ];

        foreach (array_reverse($ranks) as $rank) {
            Rank::factory()->state($rank)->create();
        }
    }
}
