<?php

namespace Database\Seeders\Demo\Military;

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
                'name' => 'Special Forces Officer',
                'abbreviation' => '18A',
                'description' => 'As a Special Forces Officer, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You will lead teams on missions, including counter-terrorism, direct action, foreign internal defense, intelligence gathering, and unconventional warfare. You’ll have several duties, including training, resource management, mission and logistics planning, and working with U.S. and foreign government agencies.',
            ],
            [
                'name' => 'Special Forces Warrant Officer',
                'abbreviation' => '180A',
                'description' => 'Special Forces (SF) Warrant Officers are combat leaders and staff officers. They are experienced subject matter experts in unconventional warfare, operations and intelligence fusion, and planning and execution at all levels across the operational continuum.',
            ],
            [
                'name' => 'Special Forces Weapons Sergeant',
                'abbreviation' => '18B',
                'description' => 'As a Special Forces Weapons Sergeant, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You will operate and maintain a wide variety of domestic (United States), allied, and foreign weaponry. You’ll employ conventional and unconventional warfare tactics and techniques in individual and small arms infantry operations.',
            ],
            [
                'name' => 'Special Forces Engineer Sergeant',
                'abbreviation' => '18C',
                'description' => 'As a Special Forces Engineer Sergeant, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You will serve on construction projects, building critical infrastructure and creating bridges, buildings, and field barricades. As a demolitions specialist, you’ll carry out demolition raids against strategic enemy targets like railroads, fuel depots, and bridges, destroying critical components of infrastructure to give our Soldiers a tactical advantage.',
            ],
            [
                'name' => 'Special Forces Medical Sergeant',
                'abbreviation' => '18D',
                'description' => 'As a Special Forces Medical Sergeant, you\'ll become Green Berets, one of the most highly skilled Soldiers in the world. Though you’ll primarily train with an emphasis on first-response and trauma medicine much like a paramedic in the civilian world, you’ll also have a working knowledge of dentistry, veterinary care, public sanitation, water quality, and optometry.',
            ],
            [
                'name' => 'Special Forces Communications Sergeant',
                'abbreviation' => '18E',
                'description' => 'As a Special Forces Communications Sergeant, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You’ll supervise communications for special operations and missions. You’ll organize, train, advise, and supervise the installation, use, and operation of communications equipment, and establish and maintain tactical lines of communication with teams during missions.',
            ],
            [
                'name' => 'Special Forces Intelligence Sergeant',
                'abbreviation' => '18F',
                'description' => 'As a Special Forces Intelligence Sergeant, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You’ll collect intelligence for special missions by employing conventional and unconventional warfare tactics and strategies, both in preparation for special missions and during operations, and provide tactical guidance to Army personnel. You’ll also be tasked with preparing reports for intelligence nets (agents who process prisoners of war, establish security plans, and maintain classified documents).',
            ],
        ];

        foreach ($specialties as $specialty) {
            Specialty::factory()->state($specialty)->create();
        }
    }
}
