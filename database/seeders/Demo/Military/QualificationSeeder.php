<?php

namespace Database\Seeders\Demo\Military;

use App\Models\Qualification;
use Illuminate\Database\Seeder;

class QualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qualifications = [
            [
                'name' => 'Airborne School',
                'description' => 'The U.S. Army Airborne School is at the U.S. Army Infantry Center, Fort Benning Georgia. This course is designed to train Soldiers to become paratroopers. It develops the student\'s confidence through repetitious training so that the student can overcome the natural fear of jumping from an airplane; develop and maintain the high level of physical fitness required of a paratrooper, by rigorous and progressive physical training. Each student must satisfactorily complete 5 jumps from an aircraft while in flight.',
            ],
            [
                'name' => 'Army Medical Department School',
                'description' => 'The U.S. Army Medical Center of Excellence, or MEDCoE, located at Fort Sam Houston, TX is the largest medical education and training campus in the world producing nearly 30,000 medical profession Medical Services Traininggraduates every year. 360 programs of instruction covering the entire range of Army Medical Department Corps: Medical, Dental, Army Nurse, Veterinary, Medical Service, and Army Medical Specialist Corps are taught.',
            ],
            [
                'name' => 'Pathfinder School',
                'description' => 'The Pathfinder School trains Army Pathfinders are trained to provide navigational aid and advisory services to military aircraft in areas designated by supported unit commanders.  The Pathfinders\' secondary missions include providing advice and limited aid to units planning air assault or airdrop operations.  During the Pathfinder course students are instructed in aircraft orientation, aero-medical evacuation, close combat assault, ground to air communication procedures, Control Center operations, all three phases of a sling load operation, Helicopter Landing Zone and Pick Up Zone operations, and Drop Zone operations (Computed Air Release Point, Ground Marker Release System, and Verbally Initiated Release System), dealing with U.S. military fixed and rotary wing aircraft for personnel and equipment.',
            ],
            [
                'name' => 'Ranger School',
                'description' => 'An Army service school, located at Fort Benning, GA teaches the fundamentals of small unit leadership and patrolling. Ranger School is the most physically and mentally demanding leadership school the Army has Ranger Schoolto offer. Trains both Officers and enlisted Soldiers through a two month course on combat arms related functional skills necessary to lead difficult missions. Training at this school is not MOS dependent. It is a prerequisite for Soldiers to have completed Airborne School.',
            ],
            [
                'name' => 'Special Forces Assessment and Selection',
                'description' => 'The Special Forces career management field (CMF) 18 includes positions concerned with the employment of highly specialized elements to accomplish specifically directed missions in times of peace and war. Many of these missions are conducted at times when employment of conventional military forces is not feasible or is not considered to be in the best interest of the United States. Training for and participation in these missions are arduous, somewhat hazardous, and often sensitive in nature. For these reasons, it is a prerequisite that every prospective Green Beret successfully completes the 19-day SFAS course and is selected for Special Forces training.',
            ],
            [
                'name' => 'Special Forces Qualification Course',
                'description' => 'Following successful completion of Special Forces Assessment and Selection (SFAS) and any other prerequisite courses, selected Soldiers will be scheduled to attend Special Forces Qualification Course (SFQC). SFQC focuses on core Special Forces tactical competencies in support of surgical strike and special warfare; Career Management Field 18 MOS classification; Survival, Evasion, Resistance and Escape (SERE); language proficiency; and regional cultural understanding. The qualification course consists of six sequential phases of training, upon completion of which Soldiers earn the right to join the Special Forces brotherhood, wear the Special Forces tab and don the green beret.',
            ],
        ];

        foreach ($qualifications as $qualification) {
            Qualification::factory()->state($qualification)->create();
        }
    }
}
