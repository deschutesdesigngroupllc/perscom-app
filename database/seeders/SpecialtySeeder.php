<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specialties = [
            [
                'name' => 'Infantryman',
                'abbreviation' => '11B',
                'description' => 'Infantrymen supervise, lead, or serve infantry activity, including employing small arms weapons or heavy anti-armor, crew-served weapons.',
            ],
            [
                'name' => 'Indirect Fire Infantrymen',
                'abbreviation' => '11C',
                'description' => 'Indirect Fire Infantrymen implement combat orders and oversee the construction of mortar ground positions.',
            ],
            [
                'name' => 'Firefighter',
                'abbreviation' => '12M',
            ],
            [
                'name' => 'Combat Engineer',
                'abbreviation' => '12B',
            ],
            [
                'name' => 'Field Support Specialist',
                'abbreviation' => '13F',
            ],
            [
                'name' => 'Information Technology Specialist',
                'abbreviation' => '25B',
            ],
            [
                'name' => 'Electronic Warfare Specialist',
                'abbreviation' => '29E',
            ],
            [
                'name' => 'Military Police',
                'abbreviation' => '31B',
            ],
            [
                'name' => 'Intelligence Analyst',
                'abbreviation' => '35F',
            ],
            [
                'name' => 'Health Care Specialist',
                'abbreviation' => '68W',
            ],
        ];

        foreach ($specialties as $specialty) {
            Specialty::factory()->state($specialty)->create();
        }
    }
}
