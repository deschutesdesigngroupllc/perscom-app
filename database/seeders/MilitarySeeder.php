<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AssignmentRecord;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Calendar;
use App\Models\CombatRecord;
use App\Models\Credential;
use App\Models\Document;
use App\Models\Enums\CredentialType;
use App\Models\Enums\FieldOptionsType;
use App\Models\Enums\FieldType;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\Group;
use App\Models\Issuer;
use App\Models\Newsfeed;
use App\Models\Page;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Slot;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Task;
use App\Models\TrainingRecord;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MilitarySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        Announcement::factory()
            ->state([
                'title' => 'Welcome to the PERSCOM Military Demo',
                'content' => 'Take a look around, and if you have any questions, please reach out to support@deschutesdesigngroup.com.',
                'color' => '#2563eb',
                'global' => true,
            ])
            ->create();

        Announcement::factory()
            ->state([
                'title' => 'OPORD 25-04 - Exercise Robin Sage',
                'content' => 'All ODA personnel will report in CIF-issued kit no later than 0500 on D-Day. Final back-briefs will be conducted by Detachment Commanders the night prior.',
                'color' => '#facc15',
                'global' => true,
            ])
            ->create();

        $documents = Document::factory()
            ->count(6)
            ->recycle($user)
            ->sequence(
                ['name' => 'FM 3-18 - Special Forces Operations', 'description' => 'Doctrinal publication describing the role, organization, and employment of U.S. Army Special Forces.'],
                ['name' => 'TC 18-01 - Special Forces Unconventional Warfare', 'description' => 'Training circular outlining unconventional warfare doctrine and partnered force employment.'],
                ['name' => 'ODA Standard Operating Procedures', 'description' => 'Detachment-level SOPs covering load plans, mission planning, communications, and battle drills.'],
                ['name' => 'OPORD 25-04 - Exercise Robin Sage', 'description' => 'Five paragraph operations order for the culminating field training exercise of the SFQC.'],
                ['name' => 'AR 600-8-22 - Military Awards', 'description' => 'Army regulation governing eligibility, criteria, and processing of military awards and decorations.'],
                ['name' => 'Detachment Mission Brief - Operation Sandfly', 'description' => 'Confidential mission planning brief covering FID partnered training in CENTCOM AOR.'],
            )
            ->create();

        $positions = Position::factory()
            ->count(8)
            ->sequence(
                ['name' => 'Detachment Commander', 'order' => 1],
                ['name' => 'Assistant Detachment Commander', 'order' => 2],
                ['name' => 'Operations Sergeant', 'order' => 3],
                ['name' => 'Assistant Operations and Intelligence Sergeant', 'order' => 4],
                ['name' => 'Weapons Sergeant', 'order' => 5],
                ['name' => 'Communications Sergeant', 'order' => 6],
                ['name' => 'Medical Sergeant', 'order' => 7],
                ['name' => 'Engineering Sergeant', 'order' => 8],
            )
            ->create();

        $specialties = Specialty::factory()
            ->count(8)
            ->sequence(
                [
                    'name' => 'Special Forces Officer',
                    'abbreviation' => '18A',
                    'description' => 'Commands an ODA, responsible for planning, executing, and leading Special Forces operations worldwide.',
                    'order' => 1,
                ], [
                    'name' => 'Special Forces Warrant Officer',
                    'abbreviation' => '180A',
                    'description' => 'Serves as the assistant detachment commander, specializing in mission planning, unconventional warfare, and operational leadership.',
                    'order' => 2,
                ], [
                    'name' => 'Special Forces Operations Sergeant',
                    'abbreviation' => '18Z',
                    'description' => 'A senior enlisted leader of an ODA, responsible for mission planning, training, and operational execution.',
                    'order' => 3,
                ], [
                    'name' => 'Special Forces Weapons Sergeant',
                    'abbreviation' => '18B',
                    'description' => 'Expert in U.S. and foreign weapons, small-unit tactics, and training partner forces in combat operations.',
                    'order' => 4,
                ], [
                    'name' => 'Special Forces Engineer Sergeant',
                    'abbreviation' => '18C',
                    'description' => 'Skilled in demolitions, construction, fortifications, and mobility operations to support mission objectives.',
                    'order' => 5,
                ], [
                    'name' => 'Special Forces Medical Sergeant',
                    'abbreviation' => '18D',
                    'description' => 'Provides advanced trauma care, prolonged field care, and medical training in austere environments.',
                    'order' => 6,
                ], [
                    'name' => 'Special Forces Communications Sergeant',
                    'abbreviation' => '18E',
                    'description' => 'Manages radio, satellite, and cyber communications to ensure secure and reliable team connectivity.',
                    'order' => 7,
                ], [
                    'name' => 'Special Forces Intelligence Sergeant',
                    'abbreviation' => '18F',
                    'description' => 'Conducts intelligence gathering, analysis, and target development to support mission planning and execution.',
                    'order' => 8,
                ],
            )
            ->create();

        $units = Unit::factory()
            ->count(5)
            ->hasAttached(Slot::factory()
                ->sequence(
                    ['name' => 'Group Commander', 'specialty_id' => 1, 'position_id' => null, 'order' => 1],
                    ['name' => 'Executive Officer', 'specialty_id' => 1, 'position_id' => null, 'order' => 2],
                    ['name' => 'Group Command Sergeant Major', 'specialty_id' => null, 'position_id' => null, 'order' => 3],
                    ['name' => 'Company Commander', 'specialty_id' => 1, 'position_id' => null, 'order' => 4],
                    ['name' => 'Executive Officer', 'specialty_id' => 1, 'position_id' => null, 'order' => 5],
                    ['name' => 'First Sergeant', 'specialty_id' => null, 'position_id' => null, 'order' => 6],
                    ['name' => 'Detachment Commander', 'specialty_id' => 1, 'position_id' => 1, 'order' => 7],
                    ['name' => 'Assistant Detachment Commander', 'specialty_id' => 2, 'position_id' => 2, 'order' => 8],
                    ['name' => 'Operations Sergeant', 'specialty_id' => 3, 'position_id' => 3, 'order' => 9],
                    ['name' => 'Detachment Commander', 'specialty_id' => 1,  'position_id' => 1, 'order' => 10],
                    ['name' => 'Operations Sergeant', 'specialty_id' => 3, 'position_id' => 3, 'order' => 11],
                    ['name' => 'Weapons Sergeant', 'specialty_id' => 4, 'position_id' => 5, 'order' => 12],
                    ['name' => 'Detachment Commander', 'specialty_id' => 1,  'position_id' => 1, 'order' => 13],
                    ['name' => 'Engineering Sergeant', 'specialty_id' => 5, 'position_id' => 8, 'order' => 14],
                    ['name' => 'Medical Sergeant', 'specialty_id' => 6, 'position_id' => 7, 'order' => 15],
                )
                ->count(3)
            )
            ->sequence(
                ['name' => 'Headquarters and Headquarters Company, 5th Special Forces Group', 'order' => 1],
                ['name' => 'Alpha Company, 1st Btn, 5th SFG', 'order' => 2],
                ['name' => 'ODB 5110, A Co, 1st Btn, 5th SFG', 'order' => 3],
                ['name' => 'ODA 5111, A Co, 1st Btn, 5th SFG', 'order' => 4],
                ['name' => 'ODA 5112, A Co, 1st Btn, 5th SFG', 'order' => 5],
            )
            ->create();

        Group::factory()
            ->state([
                'name' => 'Operations',
                'icon' => 'heroicon-o-fire',
                'order' => 1,
            ])
            ->hasAttached($units)
            ->create();

        Group::factory()
            ->state([
                'name' => 'Training',
                'icon' => 'heroicon-o-academic-cap',
                'order' => 2,
            ])
            ->hasAttached(Unit::factory()
                ->state([
                    'name' => 'TRADOC',
                    'order' => 6,
                ])
                ->hasAttached(Slot::factory()
                    ->state([
                        'name' => 'Training Command',
                        'empty' => 'No current openings.',
                    ])
                )
            )
            ->create();

        $awards = Award::factory()
            ->count(8)
            ->sequence(
                [
                    'name' => 'Distinguished Service Cross',
                    'description' => 'The Army Distinguished Service Cross Medal (DSC) is a U.S. Army decoration given for extreme gallantry and risk of life in actual combat with an armed enemy force. Operations which merit the DSC need to be of such a high degree to be above those mandatory for all other U.S. combat decorations but not meeting the criteria for the Medal of Honor. The DSC is equivalent to the Navy Cross (Navy and Marine Corps) and the Air Force Cross (Air Force). The DSC was first established and awarded during World War I. In accession, a number of awards were delegated for actions preceding World War I. In many cases, these were to soldiers who had acquired a Certificate of merit for gallantry which, at the time, was the only other honor beyond the Medal of Honor the Army could give. Others were delayed acknowledgement of actions in the Philippines, on the Mexican Border and during the Boxer Rebellion. This decoration should not be mistaken for the Distinguished Service Medal, which distinguishes meritorious service to the government of the U.S. (as a senior military officer or government official) rather than individual acts of bravery (as a member of the U.S. Army).',
                ], [
                    'name' => 'Defense Distinguished Service Medal',
                    'description' => "The Defense Distinguished Service Medal (DDSM) is presented to any member of the U.S. Armed Forces, while serving with the Department of Defense, who distinguishes themselves with exceptional performance of duty contributing to national security or defense of the United States. Created on July 9th, 1970 by President Richard Nixon's Executive Order 11545, the medals is typically awarded to senior officers such as the Chairman and Vice Chairman of the Joint Chiefs of Staff, the Chief and Vice Chiefs of the military services and other personnel whose duties bring them in direct and frequent contact with the Secretary of Defense, Deputy Secretary of Defense or other senior government officials.",
                ], [
                    'name' => 'Silver Star',
                    'description' => '',
                ], [
                    'name' => 'Defense Superior Service Medal',
                    'description' => 'The Defense Superior Service Medal (DSSM) is the second highest award bestowed by the Department of Defense. Awarded in the name of the Secretary of Defense, the award is presented to members of the U.S. Armed Forces who perform "superior meritorious service in a position of significant responsibility."  Created on February 6th, 1976 by President Gerald R. Ford\'s Executive Order 11904, it is typically awarded only to senior officers of the Flag and General Officer grade.',
                ], [
                    'name' => 'Good Conduct Medal',
                    'description' => 'The Good Conduct Medal (GCM) is awarded to members of the United States military for exemplary behavior, efficiency, and fidelity during a specified period of active service. The medal is selective and is not automatically awarded. Recipients must also have excellent or higher character and efficiency ratings, and have not been convicted by court martial during the qualifying period.',
                ], [
                    'name' => 'Bronze Star',
                    'description' => 'The Bronze Star Medal (BSM or BSV) is an award presented to United States Armed Forces personnel for bravery, acts of merit or meritorious service. When awarded for combat heroism it is awarded with a V device for Valor. It is the fourth highest combat award of the Armed Forces.',
                ], [
                    'name' => 'Purple Heart',
                    'description' => 'The Purple Heart Medal (PH) is a decoration presented in the name of the President of the United States to recognize members of the U.S. military who have been wounded or killed in battle. It differs from other military decorations in that a "recommendation" from a superior is not required, but rather individuals are entitled based on meeting certain criteria found in AR 600-8-22. This criteria was expanded on March 28, 1973 to include injuries received as a result of an international terrorist attack against the U.S. and while serving outside the territory of the U.S. as part of a peacekeeping force. Personnel wounded or killed by friendly fire are also eligible for this award as long as the injuries were received in combat and with the intention of inflicting harm on the opposing forces. The Purple Heart is not awarded for non-combat injuries and commanders must take into account the extent of enemy involvement in the wound.',
                ], [
                    'name' => 'Meritorious Service Medal',
                    'description' => 'The Meritorious Service Medal (MSM) is a decoration presented by the United States Armed Forces to recognize superior and exceptional non-combat service that does not meet the caliber of the Legion of Merit Medal. As of September 11, 2001, this award may also be issued for outstanding service in specific combat theater. The majority of recipients are field grade officers, senior warrant officers, senior non-commissioned officers and foreign military personnel in the ranks of O-6 and below. Subsequent awards are denoted by bronze oak leafs for Army and Air Force members, and gold stars for Navy, Marine Corps and Coast Guard members.',
                ],
            )
            ->create()
            ->each(function (Award $award) {
                $path = "awards/$award->name.png";
                $image = storage_path("app/images/awards/$award->name.png");

                if (! Storage::exists($path) && file_exists($image)) {
                    if ($file = file_get_contents($image)) {
                        Storage::put(
                            path: $path,
                            contents: $file,
                            options: 'public'
                        );
                    }
                }

                $award->image()->create([
                    'path' => $path,
                    'name' => $award->name,
                    'filename' => "$award->name.png",
                ]);
            });

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
            ->sequence(
                ['name' => 'Range Day - M4 / M249 Qualification'],
                ['name' => 'HALO Sustainment Jump'],
                ['name' => 'CQB Lane Training'],
                ['name' => 'Combat Lifesaver Recertification'],
                ['name' => 'Mission Planning - OPORD 25-04'],
                ['name' => 'Robin Sage Pre-Mission Brief'],
                ['name' => 'PT Test - ACFT'],
                ['name' => 'Foreign Weapons Familiarization'],
                ['name' => 'Land Navigation - Day / Night'],
                ['name' => 'Detachment AAR'],
            )
            ->for($user, 'author')
            ->create();

        $fields = Field::factory()
            ->count(5)
            ->sequence(
                ['name' => 'DoD ID Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast()],
                ['name' => 'Airborne Qualified', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast()],
                ['name' => 'Date of Rank', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast()],
                ['name' => 'AKO / Personal Email', 'type' => FieldType::FIELD_EMAIL, 'cast' => FieldType::FIELD_EMAIL->getCast()],
                ['name' => 'Duty Time Zone', 'type' => FieldType::FIELD_TIMEZONE, 'cast' => FieldType::FIELD_TIMEZONE->getCast()],
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
            ->create()
            ->each(function (Qualification $qualification) {
                $path = "qualifications/$qualification->name.png";
                $image = storage_path("app/images/qualifications/$qualification->name.png");

                if (! Storage::exists($path) && file_exists($image)) {
                    if ($file = file_get_contents($image)) {
                        Storage::put(
                            path: $path,
                            contents: $file,
                            options: 'public'
                        );
                    }
                }

                $qualification->image()->create([
                    'path' => $path,
                    'name' => $qualification->name,
                    'filename' => "$qualification->name.png",
                ]);
            });

        $ranks = Rank::factory()
            ->count(7)
            ->sequence(
                [
                    'name' => 'Sergeant',
                    'abbreviation' => 'SGT',
                    'paygrade' => 'E-5',
                    'order' => 7,
                ], [
                    'name' => 'Staff Sergeant',
                    'abbreviation' => 'SSG',
                    'paygrade' => 'E-6',
                    'order' => 6,
                ], [
                    'name' => 'Sergeant First Class',
                    'abbreviation' => 'SFC',
                    'paygrade' => 'E-7',
                    'order' => 5,
                ], [
                    'name' => 'Master Sergeant',
                    'abbreviation' => 'MSG',
                    'paygrade' => 'E-8',
                    'order' => 4,
                ], [
                    'name' => 'First Sergeant',
                    'abbreviation' => '1SG',
                    'paygrade' => 'E-8',
                    'order' => 3,
                ], [
                    'name' => 'Sergeant Major',
                    'abbreviation' => 'SGM',
                    'paygrade' => 'E-9',
                    'order' => 2,
                ], [
                    'name' => 'Command Sergeant Major',
                    'abbreviation' => 'CSM',
                    'paygrade' => 'E-9',
                    'order' => 1,
                ],
            )
            ->create()
            ->each(function (Rank $rank) {
                $path = "ranks/$rank->abbreviation.svg";
                $image = storage_path("app/images/ranks/$rank->name.png");

                if (! Storage::exists($path) && file_exists($image)) {
                    if ($file = file_get_contents($image)) {
                        Storage::put(
                            path: $path,
                            contents: $file,
                            options: 'public'
                        );
                    }
                }

                $rank->image()->create([
                    'path' => $path,
                    'name' => $rank->name,
                    'filename' => "$rank->abbreviation.svg",
                ]);
            });

        $statuses = Status::factory()
            ->count(3)
            ->sequence(
                [
                    'name' => 'Active',
                    'color' => '#16a34a',
                    'icon' => 'heroicon-o-fire',
                ], [
                    'name' => 'Inactive',
                    'color' => '#dc2626',
                ], [
                    'name' => 'On Leave',
                    'color' => '#0284c7',
                ],
            )
            ->create();

        $tasks = Task::factory()
            ->count(4)
            ->sequence(
                ['title' => 'Submit After Action Report', 'description' => 'Document and submit AAR for the most recent training event or mission per detachment SOP.'],
                ['title' => 'Update DD Form 93 / SGLI', 'description' => 'Verify Record of Emergency Data and SGLI election with the S1 prior to next deployment cycle.'],
                ['title' => 'Complete Annual Weapons Qualification', 'description' => 'Coordinate with the Range OIC and complete annual M4 / sidearm qualification.'],
                ['title' => 'Attend Promotion Ceremony', 'description' => 'Attend the quarterly promotion and reenlistment ceremony in Class A uniform.'],
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
                ->count(5), 'assignment_records')
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
                ->count(5), 'qualification_records')
            ->has(RankRecord::factory()
                ->for($user, 'author')
                ->recycle([$ranks, $documents])
                ->count(5), 'rank_records')
            ->has(ServiceRecord::factory()
                ->for($user, 'author')
                ->recycle($documents)
                ->count(5), 'service_records')
            ->has(TrainingRecord::factory()
                ->for($user, 'author')
                ->for($user, 'instructor')
                ->recycle($documents)
                ->count(5), 'training_records')
            ->hasAttached($tasks->random(3), ['assigned_by_id' => $user->getKey(), 'assigned_at' => now()])
            ->hasAttached($events->random(3))
            ->hasAttached($fields->take(3))
            ->create();

        Form::factory()
            ->state([
                'name' => 'Personnel Action Request (DA Form 4187)',
                'slug' => 'personnel-action-request',
                'description' => 'Submit personnel actions in accordance with DA PAM 600-8, including reassignment, MOS reclassification, separation, and name changes.',
                'instructions' => "Complete all sections in full. Provide supporting documentation under the Justification section. Routing:\n\n1. Soldier completes the request below.\n2. Immediate supervisor endorses.\n3. Detachment Commander reviews and forwards to S1.\n4. S1 staffs the action through the appropriate approval authority.\n\nIncomplete packets will be returned without action.",
                'success_message' => 'Your DA 4187 has been submitted and routed to your immediate supervisor. You will receive notifications as the action progresses.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(8)
                ->sequence(
                    ['name' => 'Soldier Full Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'LAST, FIRST MI'],
                    ['name' => 'Rank / Grade', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'e.g. SFC / E-7'],
                    ['name' => 'DoD ID Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => '10-digit DoD ID'],
                    ['name' => 'Unit of Assignment', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'e.g. ODA 5111, A Co, 1st Btn, 5th SFG'],
                    ['name' => 'Action Requested', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Promotion', 'Reassignment', 'MOS Reclassification', 'Separation / ETS', 'Retirement', 'Name Change', 'Other'])],
                    ['name' => 'Effective Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Justification', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Provide a clear, concise justification for the requested action.'],
                    ['name' => 'Supporting Documentation', 'type' => FieldType::FIELD_FILE, 'cast' => FieldType::FIELD_FILE->getCast(), 'required' => false, 'help' => 'Attach memoranda, orders, or other supporting documents.'],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'After Action Report',
                'slug' => 'after-action-report',
                'description' => 'Document training events, deployments, and operational missions to capture observations, lessons learned, and recommended improvements.',
                'instructions' => 'Submit within 72 hours of mission or training completion. Do not include classified information; coordinate with the S2 if classified annexes are required.',
                'success_message' => 'AAR received. Lessons learned will be staffed through the operations cell and added to the unit knowledge base.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(8)
                ->sequence(
                    ['name' => 'Mission / Event Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'e.g. Exercise Robin Sage'],
                    ['name' => 'Mission Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Location / Training Area', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'Grid coordinate or named area'],
                    ['name' => 'Reporting Detachment', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Mission Summary', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Task, purpose, and end state. Include task organization and key personnel.'],
                    ['name' => 'Sustains - What Worked', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                    ['name' => 'Improves - What Did Not', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                    ['name' => 'Recommendations', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Recommended changes to TTPs, equipment, or training for the next iteration.'],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Award Recommendation (DA Form 638)',
                'slug' => 'award-recommendation',
                'description' => 'Recommend a soldier for an individual decoration in accordance with AR 600-8-22.',
                'instructions' => 'Provide a clearly written narrative-style proposed citation and achievement summary. Non-impact awards must be submitted no later than 30 days prior to the proposed presentation date.',
                'success_message' => 'Award recommendation submitted. The packet has been routed to the first endorser in the chain of command.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(8)
                ->sequence(
                    ['name' => 'Recommended Soldier - Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Recommended Soldier - Rank', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Recommended Soldier - DoD ID', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Award Recommended', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Bronze Star', 'Silver Star', 'Distinguished Service Cross', 'Meritorious Service Medal', 'Army Commendation Medal', 'Army Achievement Medal', 'Purple Heart'])],
                    ['name' => 'Period of Service - From', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Period of Service - To', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Proposed Citation', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Narrative-style citation, written in third person, that will appear on the certificate.'],
                    ['name' => 'Achievement Summary', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Detailed bullets describing each act, achievement, or period of service that justifies the award.'],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Leave Request (DA Form 31)',
                'slug' => 'leave-request',
                'description' => 'Request ordinary, emergency, convalescent, or permissive leave in accordance with AR 600-8-10.',
                'instructions' => 'Submit at least 30 days in advance for ordinary leave. Personnel are required to sign in at the orderly room within 24 hours of return.',
                'success_message' => 'Leave request submitted to your chain of command. Do not depart until you receive an approved DA 31.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(7)
                ->sequence(
                    ['name' => 'Soldier Full Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Type of Leave', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Ordinary', 'Emergency', 'Convalescent', 'Permissive TDY', 'Terminal'])],
                    ['name' => 'Departure Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Return Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Leave Address', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Street address, city, state, ZIP and a daytime phone number for the leave location.'],
                    ['name' => 'Emergency Contact Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Emergency Contact Phone', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Special Forces Recruitment Application',
                'slug' => 'recruitment-application',
                'description' => 'Initial application for prospective Special Forces candidates interested in attending SFAS and the Q-Course.',
                'instructions' => 'Provide accurate biographical, medical, and prior service information. Falsification of any portion of this application is grounds for immediate disqualification. A recruiter will contact you within five business days.',
                'success_message' => 'Thank you for your interest in U.S. Army Special Forces. A recruiter will contact you within five business days.',
                'is_public' => true,
            ])
            ->hasAttached(Field::factory()
                ->count(8)
                ->sequence(
                    ['name' => 'Full Legal Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Email Address', 'type' => FieldType::FIELD_EMAIL, 'cast' => FieldType::FIELD_EMAIL->getCast(), 'required' => true],
                    ['name' => 'Date of Birth', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Country of Citizenship', 'type' => FieldType::FIELD_COUNTRY, 'cast' => FieldType::FIELD_COUNTRY->getCast(), 'required' => true],
                    ['name' => 'Prior Military Service', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                    ['name' => 'Current Branch / MOS', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => false, 'help' => 'Leave blank if no prior service.'],
                    ['name' => 'Why do you want to be Special Forces?', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                    ['name' => 'DD Form 214 (if applicable)', 'type' => FieldType::FIELD_FILE, 'cast' => FieldType::FIELD_FILE->getCast(), 'required' => false],
                )
            )
            ->create();

        Newsfeed::factory()
            ->state([
                'event' => null,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'headline' => 'Detachment Reaches Full MTOE Strength',
                    'text' => '5th Special Forces Group has successfully filled all primary ODA positions for the upcoming rotation. Welcome aboard to all newly assigned operators.',
                ],
            ])
            ->for($user, 'causer')
            ->create();

        Newsfeed::factory()
            ->state([
                'event' => null,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'headline' => 'Promotions Approved',
                    'text' => 'The most recent promotion board results have been published. Congratulations to those selected for promotion to the next grade.',
                ],
            ])
            ->for($user, 'causer')
            ->create();

        $issuer = Issuer::factory()
            ->state([
                'name' => 'Department of the Army',
            ])
            ->create();

        Credential::factory()
            ->count(3)
            ->for($issuer)
            ->sequence(
                [
                    'name' => 'Emergency Medical Technician (EMT)',
                    'type' => CredentialType::Certification,
                ],
                [
                    'name' => 'Commercial Driver\'s License (CDL)',
                    'type' => CredentialType::License,
                ],
                [
                    'name' => 'Top Secret Security Clearance',
                    'type' => CredentialType::Other,
                ]
            )
            ->create();

        Page::factory()
            ->count(5)
            ->sequence(
                [
                    'name' => 'Awards',
                    'description' => 'This is a custom page that has been built to display awards using the PERSCOM widgets.',
                    'slug' => 'awards',
                    'hidden' => false,
                    'icon' => 'heroicon-o-trophy',
                    'order' => 1,
                    'content' => <<<'HTML'
<!-- This HTML uses Alpine.js to dynamically build the widget -->
<div x-data="{
      init() {
        const script = document.createElement('script');
        script.id = 'perscom_widget';
        script.src = '{{ widgetUrl() }}';
        script.type = 'text/javascript';
        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
        script.setAttribute('data-widget', 'awards');

        if (document.documentElement.classList.contains('dark')) {
            script.setAttribute('data-dark', 'true');
        }

        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
      }
  }" x-init="init">
    <div id="perscom_widget_wrapper"></div>
</div>
HTML
                ],
                [
                    'name' => 'Positions',
                    'description' => 'This is a custom page that has been built to display positions using the PERSCOM widgets.',
                    'slug' => 'positions',
                    'hidden' => false,
                    'icon' => 'heroicon-o-identification',
                    'order' => 2,
                    'content' => <<<'HTML'
<!-- This HTML uses Alpine.js to dynamically build the widget -->
<div x-data="{
      init() {
        const script = document.createElement('script');
        script.id = 'perscom_widget';
        script.src = '{{ widgetUrl() }}';
        script.type = 'text/javascript';
        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
        script.setAttribute('data-widget', 'positions');

        if (document.documentElement.classList.contains('dark')) {
            script.setAttribute('data-dark', 'true');
        }

        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
      }
  }" x-init="init">
    <div id="perscom_widget_wrapper"></div>
</div>
HTML
                ],
                [
                    'name' => 'Qualifications',
                    'description' => 'This is a custom page that has been built to display qualifications using the PERSCOM widgets.',
                    'slug' => 'qualifications',
                    'hidden' => false,
                    'icon' => 'heroicon-o-star',
                    'order' => 3,
                    'content' => <<<'HTML'
<!-- This HTML uses Alpine.js to dynamically build the widget -->
<div x-data="{
      init() {
        const script = document.createElement('script');
        script.id = 'perscom_widget';
        script.src = '{{ widgetUrl() }}';
        script.type = 'text/javascript';
        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
        script.setAttribute('data-widget', 'qualifications');

        if (document.documentElement.classList.contains('dark')) {
            script.setAttribute('data-dark', 'true');
        }

        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
      }
  }" x-init="init">
    <div id="perscom_widget_wrapper"></div>
</div>
HTML
                ],
                [
                    'name' => 'Ranks',
                    'description' => 'This is a custom page that has been built to display ranks using the PERSCOM widgets.',
                    'slug' => 'ranks',
                    'hidden' => false,
                    'icon' => 'heroicon-o-chevron-double-up',
                    'order' => 4,
                    'content' => <<<'HTML'
<!-- This HTML uses Alpine.js to dynamically build the widget -->
<div x-data="{
      init() {
        const script = document.createElement('script');
        script.id = 'perscom_widget';
        script.src = '{{ widgetUrl() }}';
        script.type = 'text/javascript';
        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
        script.setAttribute('data-widget', 'ranks');

        if (document.documentElement.classList.contains('dark')) {
            script.setAttribute('data-dark', 'true');
        }

        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
      }
  }" x-init="init">
    <div id="perscom_widget_wrapper"></div>
</div>
HTML
                ],
                [
                    'name' => 'Specialties',
                    'description' => 'This is a custom page that has been built to display specialties using the PERSCOM widgets.',
                    'slug' => 'specialties',
                    'hidden' => false,
                    'icon' => 'heroicon-o-briefcase',
                    'order' => 5,
                    'content' => <<<'HTML'
<!-- This HTML uses Alpine.js to dynamically build the widget -->
<div x-data="{
      init() {
        const script = document.createElement('script');
        script.id = 'perscom_widget';
        script.src = '{{ widgetUrl() }}';
        script.type = 'text/javascript';
        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
        script.setAttribute('data-widget', 'specialties');

        if (document.documentElement.classList.contains('dark')) {
            script.setAttribute('data-dark', 'true');
        }

        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
      }
  }" x-init="init">
    <div id="perscom_widget_wrapper"></div>
</div>
HTML
                ],
            )
            ->create();
    }
}
