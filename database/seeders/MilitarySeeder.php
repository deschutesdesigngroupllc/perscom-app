<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AssignmentRecord;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Calendar;
use App\Models\CombatRecord;
use App\Models\Document;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\Group;
use App\Models\Newsfeed;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class MilitarySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@perscom.io',
        ]);
        $user->assignRole('Admin');

        Announcement::factory()
            ->state([
                'title' => 'Welcome to the PERSCOM Military Demo',
                'content' => 'This is an example announcement that can be displayed to keep your entire organization up-to-date.',
                'color' => 'info',
            ])
            ->create();

        $documents = Document::factory()
            ->count(5)
            ->sequence(fn (Sequence $sequence) => ['name' => "Document $sequence->index"])
            ->create();

        $units = Unit::factory()
            ->count(8)
            ->sequence(
                ['name' => '5th Special Forces Group'],
                ['name' => 'Headquarters and Headquarters Company, 5th SFG'],
                ['name' => 'Group Support Company, 5th SFG'],
                ['name' => '1st Battalion, 5th SFG'],
                ['name' => 'Alpha Company, 1st Btn, 5th SFG'],
                ['name' => 'ODB 5110, A Co, 1st Btn, 5th SFG'],
                ['name' => 'ODA 5111, A Co, 1st Btn, 5th SFG'],
                ['name' => 'ODA 5112, A Co, 1st Btn, 5th SFG'],
            )
            ->create();

        Group::factory()
            ->state([
                'name' => 'Operations',
            ])
            ->hasAttached($units)
            ->create();

        $awards = Award::factory()
            ->count(10)
            ->sequence(
                [
                    'name' => 'Army Distinguished Service Cross',
                    'description' => 'The Army Distinguished Service Cross Medal (DSC) is a U.S. Army decoration given for extreme gallantry and risk of life in actual combat with an armed enemy force. Operations which merit the DSC need to be of such a high degree to be above those mandatory for all other U.S. combat decorations but not meeting the criteria for the Medal of Honor. The DSC is equivalent to the Navy Cross (Navy and Marine Corps) and the Air Force Cross (Air Force). The DSC was first established and awarded during World War I. In accession, a number of awards were delegated for actions preceding World War I. In many cases, these were to soldiers who had acquired a Certificate of merit for gallantry which, at the time, was the only other honor beyond the Medal of Honor the Army could give. Others were delayed acknowledgement of actions in the Philippines, on the Mexican Border and during the Boxer Rebellion. This decoration should not be mistaken for the Distinguished Service Medal, which distinguishes meritorious service to the government of the U.S. (as a senior military officer or government official) rather than individual acts of bravery (as a member of the U.S. Army).',
                ], [
                    'name' => 'Department of Defense Distinguished Service',
                    'description' => "The Defense Distinguished Service Medal (DDSM) is presented to any member of the U.S. Armed Forces, while serving with the Department of Defense, who distinguishes themselves with exceptional performance of duty contributing to national security or defense of the United States. Created on July 9th, 1970 by President Richard Nixon's Executive Order 11545, the medals is typically awarded to senior officers such as the Chairman and Vice Chairman of the Joint Chiefs of Staff, the Chief and Vice Chiefs of the military services and other personnel whose duties bring them in direct and frequent contact with the Secretary of Defense, Deputy Secretary of Defense or other senior government officials.",
                ], [
                    'name' => 'Army Distinguished Service',
                    'description' => 'The Army Distinguished Service Medal (DSM) is granted to any soldier who, while serving in the U.S. Army, distinguishes themselves with exceptionally meritorious service to the U.S. in a duty of great responsibility. The achievement must be of a level as to merit acknowledgement for service that is positively "exceptional." Exceptional performance of ordinary duties does not alone justify the award. For service not associated with actual war, the term "duty of a great responsibility" applies to a restricted range of positions than in a time of war, and commands proof of conspicuously indicative achievement.',
                ], [
                    'name' => 'Silver Star',
                    'description' => '',
                ], [
                    'name' => 'Defense Superior Service',
                    'description' => 'The Defense Superior Service Medal (DSSM) is the second highest award bestowed by the Department of Defense. Awarded in the name of the Secretary of Defense, the award is presented to members of the U.S. Armed Forces who perform "superior meritorious service in a position of significant responsibility."  Created on February 6th, 1976 by President Gerald R. Ford\'s Executive Order 11904, it is typically awarded only to senior officers of the Flag and General Officer grade.',
                ], [
                    'name' => 'Legion of Merit',
                    'description' => 'The Legion of Merit Medal (LM, LOM) is a decoration presented by the United States Armed Forces to members of the United States Military, as well as foreign military members and political figures, who have displayed exceptionally meritorious conduct in the performance of outstanding services and achievements. The performance must be of significant importance and far exceed what is expected by normal standards. When the award is presented to foreign parties, it is divided into separate ranking degrees. The degrees are as follows: Chief Commander - issued to a head of state or government; Commander - issued to a chief of staff or higher position that is not head of state; Officer - issued to a general or flag officer that is below the chief of staff, colonel or equivalent rank; Legionnaire - issued to all other service members ranking lower than those previously mentioned. Awards presented to United State military members are not divided into degrees. Subsequent awards are denoted by Oak Leaf Clusters for U.S. Army and Air Force members and Award Stars for U.S. Navy, Marine Corps and Coast Guard members. The Valor device is also authorized to be worn by the U.S. Navy, Marine Corps and Coast Guard, but not by the U.S. Army or Air Force.',
                ], [
                    'name' => 'Bronze Star',
                    'description' => 'The Bronze Star Medal (BSM or BSV) is an award presented to United States Armed Forces personnel for bravery, acts of merit or meritorious service. When awarded for combat heroism it is awarded with a V device for Valor. It is the fourth highest combat award of the Armed Forces.',
                ], [
                    'name' => 'Purple Heart',
                    'description' => 'The Purple Heart Medal (PH) is a decoration presented in the name of the President of the United States to recognize members of the U.S. military who have been wounded or killed in battle. It differs from other military decorations in that a "recommendation" from a superior is not required, but rather individuals are entitled based on meeting certain criteria found in AR 600-8-22. This criteria was expanded on March 28, 1973 to include injuries received as a result of an international terrorist attack against the U.S. and while serving outside the territory of the U.S. as part of a peacekeeping force. Personnel wounded or killed by friendly fire are also eligible for this award as long as the injuries were received in combat and with the intention of inflicting harm on the opposing forces. The Purple Heart is not awarded for non-combat injuries and commanders must take into account the extent of enemy involvement in the wound.',
                ], [
                    'name' => 'Defense Meritorious Service',
                    'description' => 'The Defense Meritorious Service Medal (DMSM) is an award presented in the name of the Secretary of Defense to members of the Armed Forces. It is the third-highest award that the Department of Defense issues, and is awarded to those who distinguish themselves though non-combat meritorious service or achievement, in a joint capacity. Created on November 3rd, 1977 by President Jimmy Carter\'s Executive Order 12019, it was first awarded to Major Terrell G Covington of the United States Army.',
                ], [
                    'name' => 'Meritorious Service',
                    'description' => 'The Meritorious Service Medal (MSM) is a decoration presented by the United States Armed Forces to recognize superior and exceptional non-combat service that does not meet the caliber of the Legion of Merit Medal. As of September 11, 2001, this award may also be issued for outstanding service in specific combat theater. The majority of recipients are field grade officers, senior warrant officers, senior non-commissioned officers and foreign military personnel in the ranks of O-6 and below. Subsequent awards are denoted by bronze oak leafs for Army and Air Force members, and gold stars for Navy, Marine Corps and Coast Guard members.',
                ],
            )
            ->create();

        $calendars = Calendar::factory()
            ->count(2)
            ->sequence(
                ['name' => 'Operations'],
                ['name' => 'Training']
            )
            ->create();

        $events = Event::factory()
            ->count(10)
            ->recycle($calendars)
            ->sequence(fn (Sequence $sequence) => ['name' => "Event $sequence->index"])
            ->for($user, 'author')
            ->create();

        $fields = Field::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Field 1', 'type' => Field::$fieldTypes[Field::FIELD_TEXT], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_TEXT], 'cast' => Field::$fieldCasts[Field::FIELD_TEXT]],
                ['name' => 'Field 2', 'type' => Field::$fieldTypes[Field::FIELD_BOOLEAN], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_BOOLEAN], 'cast' => Field::$fieldCasts[Field::FIELD_BOOLEAN]],
                ['name' => 'Field 3', 'type' => Field::$fieldTypes[Field::FIELD_DATE], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_DATE], 'cast' => Field::$fieldCasts[Field::FIELD_DATE]],
                ['name' => 'Field 4', 'type' => Field::$fieldTypes[Field::FIELD_EMAIL], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_EMAIL], 'cast' => Field::$fieldCasts[Field::FIELD_EMAIL]],
                ['name' => 'Field 5', 'type' => Field::$fieldTypes[Field::FIELD_TIMEZONE], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_TIMEZONE], 'cast' => Field::$fieldCasts[Field::FIELD_TIMEZONE]],
            )
            ->create();

        $qualifications = Qualification::factory()
            ->count(6)
            ->sequence(
                [
                    'name' => 'Airborne School',
                    'description' => 'The U.S. Army Airborne School is at the U.S. Army Infantry Center, Fort Benning Georgia. This course is designed to train Soldiers to become paratroopers. It develops the student\'s confidence through repetitious training so that the student can overcome the natural fear of jumping from an airplane; develop and maintain the high level of physical fitness required of a paratrooper, by rigorous and progressive physical training. Each student must satisfactorily complete 5 jumps from an aircraft while in flight.',
                ], [
                    'name' => 'Army Medical Department School',
                    'description' => 'The U.S. Army Medical Center of Excellence, or MEDCoE, located at Fort Sam Houston, TX is the largest medical education and training campus in the world producing nearly 30,000 medical profession Medical Services Traininggraduates every year. 360 programs of instruction covering the entire range of Army Medical Department Corps: Medical, Dental, Army Nurse, Veterinary, Medical Service, and Army Medical Specialist Corps are taught.',
                ], [
                    'name' => 'Pathfinder School',
                    'description' => 'The Pathfinder School trains Army Pathfinders are trained to provide navigational aid and advisory services to military aircraft in areas designated by supported unit commanders.  The Pathfinders\' secondary missions include providing advice and limited aid to units planning air assault or airdrop operations.  During the Pathfinder course students are instructed in aircraft orientation, aero-medical evacuation, close combat assault, ground to air communication procedures, Control Center operations, all three phases of a sling load operation, Helicopter Landing Zone and Pick Up Zone operations, and Drop Zone operations (Computed Air Release Point, Ground Marker Release System, and Verbally Initiated Release System), dealing with U.S. military fixed and rotary wing aircraft for personnel and equipment.',
                ], [
                    'name' => 'Ranger School',
                    'description' => 'An Army service school, located at Fort Benning, GA teaches the fundamentals of small unit leadership and patrolling. Ranger School is the most physically and mentally demanding leadership school the Army has Ranger Schoolto offer. Trains both Officers and enlisted Soldiers through a two month course on combat arms related functional skills necessary to lead difficult missions. Training at this school is not MOS dependent. It is a prerequisite for Soldiers to have completed Airborne School.',
                ], [
                    'name' => 'Special Forces Assessment and Selection',
                    'description' => 'The Special Forces career management field (CMF) 18 includes positions concerned with the employment of highly specialized elements to accomplish specifically directed missions in times of peace and war. Many of these missions are conducted at times when employment of conventional military forces is not feasible or is not considered to be in the best interest of the United States. Training for and participation in these missions are arduous, somewhat hazardous, and often sensitive in nature. For these reasons, it is a prerequisite that every prospective Green Beret successfully completes the 19-day SFAS course and is selected for Special Forces training.',
                ], [
                    'name' => 'Special Forces Qualification Course',
                    'description' => 'Following successful completion of Special Forces Assessment and Selection (SFAS) and any other prerequisite courses, selected Soldiers will be scheduled to attend Special Forces Qualification Course (SFQC). SFQC focuses on core Special Forces tactical competencies in support of surgical strike and special warfare; Career Management Field 18 MOS classification; Survival, Evasion, Resistance and Escape (SERE); language proficiency; and regional cultural understanding. The qualification course consists of six sequential phases of training, upon completion of which Soldiers earn the right to join the Special Forces brotherhood, wear the Special Forces tab and don the green beret.',
                ],
            )
            ->create();

        $ranks = Rank::factory()
            ->count(7)
            ->sequence(
                [
                    'name' => 'Sergeant',
                    'abbreviation' => 'SGT',
                    'paygrade' => 'E-5',
                ], [
                    'name' => 'Staff Sergeant',
                    'abbreviation' => 'SSG',
                    'paygrade' => 'E-6',
                ], [
                    'name' => 'Sergeant First Class',
                    'abbreviation' => 'SFC',
                    'paygrade' => 'E-7',
                ], [
                    'name' => 'Master Sergeant',
                    'abbreviation' => 'MSG',
                    'paygrade' => 'E-8',
                ], [
                    'name' => 'First Sergeant',
                    'abbreviation' => '1SG',
                    'paygrade' => 'E-8',
                ], [
                    'name' => 'Sergeant Major',
                    'abbreviation' => 'SGM',
                    'paygrade' => 'E-9',
                ], [
                    'name' => 'Command Sergeant Major',
                    'abbreviation' => 'CSM',
                    'paygrade' => 'E-9',
                ],
            )
            ->create();

        $specialties = Specialty::factory()
            ->count(7)
            ->sequence(
                [
                    'name' => 'Special Forces Officer',
                    'abbreviation' => '18A',
                    'description' => 'As a Special Forces Officer, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You will lead teams on missions, including counter-terrorism, direct action, foreign internal defense, intelligence gathering, and unconventional warfare. You’ll have several duties, including training, resource management, mission and logistics planning, and working with U.S. and foreign government agencies.',
                ], [
                    'name' => 'Special Forces Warrant Officer',
                    'abbreviation' => '180A',
                    'description' => 'Special Forces (SF) Warrant Officers are combat leaders and staff officers. They are experienced subject matter experts in unconventional warfare, operations and intelligence fusion, and planning and execution at all levels across the operational continuum.',
                ], [
                    'name' => 'Special Forces Weapons Sergeant',
                    'abbreviation' => '18B',
                    'description' => 'As a Special Forces Weapons Sergeant, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You will operate and maintain a wide variety of domestic (United States), allied, and foreign weaponry. You’ll employ conventional and unconventional warfare tactics and techniques in individual and small arms infantry operations.',
                ], [
                    'name' => 'Special Forces Engineer Sergeant',
                    'abbreviation' => '18C',
                    'description' => 'As a Special Forces Engineer Sergeant, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You will serve on construction projects, building critical infrastructure and creating bridges, buildings, and field barricades. As a demolitions specialist, you’ll carry out demolition raids against strategic enemy targets like railroads, fuel depots, and bridges, destroying critical components of infrastructure to give our Soldiers a tactical advantage.',
                ], [
                    'name' => 'Special Forces Medical Sergeant',
                    'abbreviation' => '18D',
                    'description' => 'As a Special Forces Medical Sergeant, you\'ll become Green Berets, one of the most highly skilled Soldiers in the world. Though you’ll primarily train with an emphasis on first-response and trauma medicine much like a paramedic in the civilian world, you’ll also have a working knowledge of dentistry, veterinary care, public sanitation, water quality, and optometry.',
                ], [
                    'name' => 'Special Forces Communications Sergeant',
                    'abbreviation' => '18E',
                    'description' => 'As a Special Forces Communications Sergeant, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You’ll supervise communications for special operations and missions. You’ll organize, train, advise, and supervise the installation, use, and operation of communications equipment, and establish and maintain tactical lines of communication with teams during missions.',
                ], [
                    'name' => 'Special Forces Intelligence Sergeant',
                    'abbreviation' => '18F',
                    'description' => 'As a Special Forces Intelligence Sergeant, you’ll become a member of the Green Berets, one of the most highly skilled Soldiers in the world. You’ll collect intelligence for special missions by employing conventional and unconventional warfare tactics and strategies, both in preparation for special missions and during operations, and provide tactical guidance to Army personnel. You’ll also be tasked with preparing reports for intelligence nets (agents who process prisoners of war, establish security plans, and maintain classified documents).',
                ],
            )
            ->create();

        $statuses = Status::factory()
            ->count(3)
            ->sequence(
                [
                    'name' => 'Active',
                    'text_color' => '#16a34a',
                    'bg_color' => '#dcfce7',
                ], [
                    'name' => 'Inactive',
                    'text_color' => '#dc2626',
                    'bg_color' => '#fee2e2',
                ], [
                    'name' => 'On Leave',
                    'text_color' => '#0284c7',
                    'bg_color' => '#e0f2fe',
                ],
            )
            ->create();

        $positions = Position::factory()
            ->count(8)
            ->sequence(
                ['name' => 'Detachment Commander'],
                ['name' => 'Assistant Detachment Commander'],
                ['name' => 'Operations Sergeant'],
                ['name' => 'Assistant Operations and Intelligence Sergeant'],
                ['name' => 'Weapons Sergeant'],
                ['name' => 'Communications Sergeant'],
                ['name' => 'Medical Sergeant'],
                ['name' => 'Engineering Sergeant'],
            )
            ->create();

        $tasks = Task::factory()
            ->count(3)
            ->sequence(
                ['title' => 'Submit After Action Report'],
                ['title' => 'Update Personal Information'],
                ['title' => 'Attend Promotion Ceremony'],
            )
            ->create();

        User::factory()
            ->count(10)
            ->recycle($positions)
            ->recycle($specialties)
            ->recycle($ranks)
            ->recycle($statuses)
            ->recycle($tasks)
            ->recycle($units)
            ->has(AssignmentRecord::factory()
                ->for($user, 'author')
                ->recycle([$positions, $specialties, $statuses, $units, $documents])
                ->count(5), 'service_records')
            ->has(AwardRecord::factory()
                ->for($user, 'author')
                ->recycle([$awards, $documents])
                ->count(5), 'award_records')
            ->has(CombatRecord::factory()
                ->for($user, 'author')
                ->recycle($documents)
                ->count(5), 'combat_records')
            ->has(QualificationRecord::factory()
                ->for($user, 'author')
                ->recycle([$qualifications, $documents])
                ->count(5), 'combat_records')
            ->has(RankRecord::factory()
                ->for($user, 'author')
                ->recycle([$ranks, $documents])
                ->count(5), 'rank_records')
            ->has(ServiceRecord::factory()
                ->for($user, 'author')
                ->recycle($documents)
                ->count(5), 'service_records')
            ->hasAttached($tasks->random(3), ['assigned_by_id' => $user->getKey(), 'assigned_at' => now()])
            ->hasAttached($events->random(3))
            ->hasAttached($positions->take(2), [], 'secondary_positions')
            ->hasAttached($specialties->take(2), [], 'secondary_specialties')
            ->hasAttached($units->take(2), [], 'secondary_units')
            ->hasAttached($fields->take(3))
            ->create();

        Newsfeed::factory()
            ->state([
                'event' => null,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'headline' => 'Backed by a powerful newsfeed',
                    'text' => 'Keep your organization up-to-date with an interactive automated newsfeed that informs personnel of recent organizational events.',
                ],
            ])
            ->for($user, 'causer')
            ->create();

        Form::factory()
            ->count(2)
            ->sequence(
                [
                    'name' => 'Personnel Action Request',
                    'description' => 'This form is used primarily for the purpose of requesting or recording personnel actions for or by soldiers in accordance with DA PAM 600-8.',
                ], [
                    'name' => 'After Action Report',
                    'description' => 'An After Action Report (AAR) is a written report that documents a unit\'s actions for historical purposes and provides key observations and lessons learned. It is typically submitted after a training mission, combat operation or other mission.',
                ],
            )
            ->hasAttached($fields->random(3))
            ->create();
    }
}
