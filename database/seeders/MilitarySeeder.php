<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\SetupTenantAccount;
use App\Models\Announcement;
use App\Models\AssignmentRecord;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Calendar;
use App\Models\CombatRecord;
use App\Models\Credential;
use App\Models\Document;
use App\Models\Enums\CredentialType;
use App\Models\Enums\FieldType;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\Group;
use App\Models\Issuer;
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
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MilitarySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /** @var SetupTenantAccount $action */
        $action = app(SetupTenantAccount::class);
        $action
            ->shouldCreateUser(false)
            ->shouldCreateAnnouncement(false)
            ->handle(tenant());

        $user = User::factory()->unassigned()->createQuietly([
            'name' => 'Demo User',
            'email' => 'demo@perscom.io',
        ]);
        $user->assignRole(Utils::getSuperAdminName());

        Announcement::factory()
            ->state([
                'title' => 'Welcome to the PERSCOM Military Demo',
                'content' => 'Take a look around, and if you have any questions, please reach out to support@deschutesdesigngroup.com.',
                'color' => '#2563eb',
                'global' => true,
            ])
            ->create();

        $documents = Document::factory()
            ->count(5)
            ->recycle($user)
            ->sequence(fn (Sequence $sequence) => ['name' => "Document $sequence->index"])
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

        $specialties = Specialty::factory()
            ->count(8)
            ->sequence(
                [
                    'name' => 'Special Forces Officer',
                    'abbreviation' => '18A',
                    'description' => 'Commands an ODA, responsible for planning, executing, and leading Special Forces operations worldwide.',
                ], [
                    'name' => 'Special Forces Warrant Officer',
                    'abbreviation' => '180A',
                    'description' => 'Serves as the assistant detachment commander, specializing in mission planning, unconventional warfare, and operational leadership.',
                ], [
                    'name' => 'Special Forces Operations Sergeant',
                    'abbreviation' => '18Z',
                    'description' => 'A senior enlisted leader of an ODA, responsible for mission planning, training, and operational execution.',
                ], [
                    'name' => 'Special Forces Weapons Sergeant',
                    'abbreviation' => '18B',
                    'description' => 'Expert in U.S. and foreign weapons, small-unit tactics, and training partner forces in combat operations.',
                ], [
                    'name' => 'Special Forces Engineer Sergeant',
                    'abbreviation' => '18C',
                    'description' => 'Skilled in demolitions, construction, fortifications, and mobility operations to support mission objectives.',
                ], [
                    'name' => 'Special Forces Medical Sergeant',
                    'abbreviation' => '18D',
                    'description' => 'Provides advanced trauma care, prolonged field care, and medical training in austere environments.',
                ], [
                    'name' => 'Special Forces Communications Sergeant',
                    'abbreviation' => '18E',
                    'description' => 'Manages radio, satellite, and cyber communications to ensure secure and reliable team connectivity.',
                ], [
                    'name' => 'Special Forces Intelligence Sergeant',
                    'abbreviation' => '18F',
                    'description' => 'Conducts intelligence gathering, analysis, and target development to support mission planning and execution.',
                ],
            )
            ->create();

        $units = Unit::factory()
            ->count(5)
            ->hasAttached(Slot::factory()
                ->sequence(
                    ['name' => 'Group Commander', 'specialty_id' => 1, 'position_id' => null],
                    ['name' => 'Executive Officer', 'specialty_id' => 1, 'position_id' => null],
                    ['name' => 'Group Command Sergeant Major', 'specialty_id' => null, 'position_id' => null],
                    ['name' => 'Company Commander', 'specialty_id' => 1, 'position_id' => null],
                    ['name' => 'Executive Officer', 'specialty_id' => 1, 'position_id' => null],
                    ['name' => 'First Sergeant', 'specialty_id' => null, 'position_id' => null],
                    ['name' => 'Detachment Commander', 'specialty_id' => 1, 'position_id' => 1],
                    ['name' => 'Assistant Detachment Commander', 'specialty_id' => 2, 'position_id' => 2],
                    ['name' => 'Operations Sergeant', 'specialty_id' => 3, 'position_id' => 3],
                    ['name' => 'Detachment Commander', 'specialty_id' => 1,  'position_id' => 1],
                    ['name' => 'Operations Sergeant', 'specialty_id' => 3, 'position_id' => 3],
                    ['name' => 'Weapons Sergeant', 'specialty_id' => 4, 'position_id' => 5],
                    ['name' => 'Detachment Commander', 'specialty_id' => 1,  'position_id' => 1],
                    ['name' => 'Engineering Sergeant', 'specialty_id' => 5, 'position_id' => 8],
                    ['name' => 'Medical Sergeant', 'specialty_id' => 6, 'position_id' => 7],
                )
                ->count(3)
            )
            ->sequence(
                ['name' => 'Headquarters and Headquarters Company, 5th Special Forces Group'],
                ['name' => 'Alpha Company, 1st Btn, 5th SFG'],
                ['name' => 'ODB 5110, A Co, 1st Btn, 5th SFG'],
                ['name' => 'ODA 5111, A Co, 1st Btn, 5th SFG'],
                ['name' => 'ODA 5112, A Co, 1st Btn, 5th SFG'],
            )
            ->create();

        Group::factory()
            ->state([
                'name' => 'Operations',
                'icon' => 'heroicon-o-fire',
            ])
            ->hasAttached($units)
            ->create();

        Group::factory()
            ->state([
                'name' => 'Training',
                'icon' => 'heroicon-o-academic-cap',
            ])
            ->hasAttached(Unit::factory()
                ->state([
                    'name' => 'TRADOC',
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
                $path = "$award->name.png";

                if (! Storage::disk('s3')->exists($path)) {
                    if ($file = file_get_contents(storage_path("app/images/awards/$award->name.png"))) {
                        Storage::disk('s3')->put(
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
            ->sequence(fn (Sequence $sequence) => ['name' => "Event $sequence->index"])
            ->for($user, 'author')
            ->create();

        $fields = Field::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Field 1', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast()],
                ['name' => 'Field 2', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast()],
                ['name' => 'Field 3', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast()],
                ['name' => 'Field 4', 'type' => FieldType::FIELD_EMAIL, 'cast' => FieldType::FIELD_EMAIL->getCast()],
                ['name' => 'Field 5', 'type' => FieldType::FIELD_TIMEZONE, 'cast' => FieldType::FIELD_TIMEZONE->getCast()],
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
            ->create()
            ->each(function (Rank $rank) {
                $path = "$rank->abbreviation.svg";

                if (! Storage::disk('s3')->exists($path)) {
                    if ($file = file_get_contents(storage_path("app/images/ranks/military/army/$rank->abbreviation.svg"))) {
                        Storage::disk('s3')->put(
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
                    'color' => '#dcfce7',
                ], [
                    'name' => 'Inactive',
                    'color' => '#fee2e2',
                ], [
                    'name' => 'On Leave',
                    'color' => '#e0f2fe',
                ],
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
            ->has(TrainingRecord::factory()
                ->for($user, 'author')
                ->for($user, 'instructor')
                ->recycle($documents)
                ->count(5), 'service_records')
            ->hasAttached($tasks->random(3), ['assigned_by_id' => $user->getKey(), 'assigned_at' => now()])
            ->hasAttached($events->random(3))
            ->hasAttached($fields->take(3))
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
    }
}
