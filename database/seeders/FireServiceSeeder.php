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

class FireServiceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        Announcement::factory()
            ->state([
                'title' => 'Welcome to the PERSCOM Fire Service Demo',
                'content' => 'Use this demo to explore how PERSCOM streamlines roster management, qualifications tracking, and training records for fire and EMS agencies. For questions, contact support@deschutesdesigngroup.com.',
                'color' => '#dc2626',
                'global' => true,
            ])
            ->create();

        Announcement::factory()
            ->state([
                'title' => 'Red Flag Warning In Effect',
                'content' => 'A Red Flag Warning has been issued for the response area. All apparatus will carry full wildland gear and additional water supply for the duration of the warning.',
                'color' => '#facc15',
                'global' => true,
            ])
            ->create();

        $documents = Document::factory()
            ->count(6)
            ->recycle($user)
            ->sequence(
                ['name' => 'SOP 100 - Incident Command System', 'description' => 'Department-wide standard operating procedure for establishing, maintaining, and transferring command on emergency incidents.'],
                ['name' => 'SOP 210 - Structure Fire Response', 'description' => 'Procedures governing initial attack, water supply, ventilation, and search operations on structural fires.'],
                ['name' => 'SOP 305 - EMS Treatment Protocols', 'description' => 'Adopted regional medical protocols for BLS and ALS patient care, including pediatric and trauma annexes.'],
                ['name' => 'SOP 410 - Hazardous Materials Response', 'description' => 'Tiered hazmat response procedures, decontamination, and coordination with regional hazmat teams.'],
                ['name' => 'Pre-Incident Plan - Memorial Hospital', 'description' => 'Building access, hydrant locations, fire protection systems, and special hazards for Memorial Hospital.'],
                ['name' => 'Wildland Operations Manual', 'description' => 'Tactics, PPE requirements, and deployment procedures for wildland and wildland-urban interface incidents.'],
            )
            ->create();

        $positions = Position::factory()
            ->count(8)
            ->sequence(
                ['name' => 'Shift Commander', 'order' => 1],
                ['name' => 'Company Officer', 'order' => 2],
                ['name' => 'Engineer / Driver-Operator', 'order' => 3],
                ['name' => 'Firefighter / Irons', 'order' => 4],
                ['name' => 'Firefighter / Nozzle', 'order' => 5],
                ['name' => 'Firefighter / OV', 'order' => 6],
                ['name' => 'Firefighter / Hydrant', 'order' => 7],
                ['name' => 'Paramedic', 'order' => 8],
            )
            ->create();

        $specialties = Specialty::factory()
            ->count(6)
            ->sequence(
                ['name' => 'Hazardous Materials Technician', 'abbreviation' => 'HMT', 'description' => 'Trained to NFPA 472 Hazmat Technician level for offensive operations on hazardous materials releases.', 'order' => 1],
                ['name' => 'Technical Rescue', 'abbreviation' => 'TRT', 'description' => 'Specialty team trained in rope, confined space, trench, and structural collapse rescue.', 'order' => 2],
                ['name' => 'Urban Search and Rescue', 'abbreviation' => 'USAR', 'description' => 'Federal task force qualified personnel responding to structural collapse and disaster operations.', 'order' => 3],
                ['name' => 'Marine / Swift Water Rescue', 'abbreviation' => 'SWR', 'description' => 'Trained in surface water rescue, swift water entry, and small boat operations.', 'order' => 4],
                ['name' => 'Wildland Firefighter', 'abbreviation' => 'WLF', 'description' => 'NWCG red-card qualified for wildland and wildland-urban interface assignments.', 'order' => 5],
                ['name' => 'Fire Investigator', 'abbreviation' => 'FI', 'description' => 'IAAI/NFPA 1033 trained investigator responsible for cause-and-origin determination.', 'order' => 6],
            )
            ->create();

        $units = Unit::factory()
            ->count(8)
            ->hasAttached(Slot::factory()
                ->sequence(
                    ['name' => 'Captain', 'specialty_id' => null, 'position_id' => 2, 'order' => 1],
                    ['name' => 'Engineer', 'specialty_id' => null, 'position_id' => 3, 'order' => 2],
                    ['name' => 'Firefighter / Irons', 'specialty_id' => null, 'position_id' => 4, 'order' => 3],
                    ['name' => 'Firefighter / Nozzle', 'specialty_id' => null, 'position_id' => 5, 'order' => 4],
                    ['name' => 'Paramedic', 'specialty_id' => null, 'position_id' => 8, 'order' => 5],
                )
                ->count(2)
            )
            ->sequence(
                ['name' => 'District 1 Headquarters', 'order' => 1],
                ['name' => 'Battalion 1', 'order' => 2],
                ['name' => 'Station 1 - Engine 1, Truck 1, Medic 1', 'order' => 3],
                ['name' => 'Station 2 - Engine 2, Medic 2', 'order' => 4],
                ['name' => 'Station 3 - Engine 3, Brush 3', 'order' => 5],
                ['name' => 'Battalion 2', 'order' => 6],
                ['name' => 'Station 4 - Engine 4, Tower 4', 'order' => 7],
                ['name' => 'Station 5 - Engine 5, Hazmat 5', 'order' => 8],
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
                'name' => 'Training & Safety',
                'icon' => 'heroicon-o-academic-cap',
                'order' => 2,
            ])
            ->hasAttached(Unit::factory()
                ->state([
                    'name' => 'Training Division',
                    'order' => 9,
                ])
                ->hasAttached(Slot::factory()
                    ->state([
                        'name' => 'Training Officer',
                        'empty' => 'Position currently open.',
                    ])
                )
            )
            ->create();

        $awards = Award::factory()
            ->count(7)
            ->sequence(
                [
                    'name' => 'Medal of Valor',
                    'description' => 'The department\'s highest honor, awarded for an act of conspicuous courage performed at extreme personal risk in the line of duty.',
                ], [
                    'name' => 'Medal of Honor',
                    'description' => 'Awarded posthumously to members who lose their lives in the line of duty while engaged in fire suppression, rescue, or emergency operations.',
                ], [
                    'name' => 'Lifesaving Medal',
                    'description' => 'Awarded to members who, through skill and decisive action, are directly responsible for saving a human life during an emergency incident.',
                ], [
                    'name' => 'Distinguished Service Award',
                    'description' => 'Recognizes sustained meritorious service or a single act of significant achievement that brings credit to the department.',
                ], [
                    'name' => 'EMS Excellence Award',
                    'description' => 'Awarded for exceptional patient care, clinical performance, or contributions to the department\'s emergency medical services program.',
                ], [
                    'name' => 'Unit Citation',
                    'description' => 'Recognizes a crew or company whose collective performance on a specific incident exemplifies teamwork and operational excellence.',
                ], [
                    'name' => 'Fire Chief Commendation',
                    'description' => 'Issued at the discretion of the Fire Chief for noteworthy service, professional achievement, or community contribution.',
                ],
            )
            ->create();

        $calendars = Calendar::factory()
            ->count(4)
            ->sequence(
                ['name' => 'Operations', 'description' => 'Shift schedules, callbacks, and operational events.', 'color' => '#dc2626'],
                ['name' => 'Training', 'description' => 'Company drills, certifications, and academy schedules.', 'color' => '#2563eb'],
                ['name' => 'Public Education', 'description' => 'Fire prevention, station tours, and community outreach.', 'color' => '#16a34a'],
                ['name' => 'Holidays', 'description' => 'Department-observed holidays and union events.', 'color' => '#facc15']
            )
            ->create();

        $events = Event::factory()
            ->count(8)
            ->recycle($calendars)
            ->sequence(
                ['name' => 'A-Shift Company Drill - Hose Lays'],
                ['name' => 'Live Fire Training - Acquired Structure'],
                ['name' => 'EMS Recertification - CPR / ACLS'],
                ['name' => 'Hazmat Refresher - PPE Donning'],
                ['name' => 'Vehicle Extrication Drill'],
                ['name' => 'Fire Prevention Week - Station Open House'],
                ['name' => 'Smoke Alarm Canvass - Eastside Neighborhood'],
                ['name' => 'Annual Apparatus Pump Testing'],
            )
            ->for($user, 'author')
            ->create();

        $fields = Field::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Incident Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast()],
                ['name' => 'Mutual Aid Response', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast()],
                ['name' => 'Date of Hire', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast()],
                ['name' => 'Personal Email', 'type' => FieldType::FIELD_EMAIL, 'cast' => FieldType::FIELD_EMAIL->getCast()],
                ['name' => 'Time Zone', 'type' => FieldType::FIELD_TIMEZONE, 'cast' => FieldType::FIELD_TIMEZONE->getCast()],
            )
            ->create();

        $qualifications = Qualification::factory()
            ->count(8)
            ->sequence(
                [
                    'name' => 'Firefighter I',
                    'description' => 'NFPA 1001 entry-level firefighter certification covering PPE, SCBA, fire behavior, ropes and knots, ladders, hose, and basic fire suppression.',
                ], [
                    'name' => 'Firefighter II',
                    'description' => 'NFPA 1001 advanced firefighter certification covering size-up, command transfer, sprinkler systems, foam operations, and fire investigation preservation.',
                ], [
                    'name' => 'Driver / Operator - Pumper',
                    'description' => 'NFPA 1002 certification for driving and operating fire department pumping apparatus, including hydraulic calculations and relay pumping.',
                ], [
                    'name' => 'Driver / Operator - Aerial',
                    'description' => 'NFPA 1002 certification for operation of aerial ladder and platform apparatus, including positioning, stabilization, and elevated stream operations.',
                ], [
                    'name' => 'Hazardous Materials Technician',
                    'description' => 'NFPA 472 Technician-level qualification authorizing offensive hazmat operations, including product identification, decontamination, and containment.',
                ], [
                    'name' => 'Confined Space Rescue',
                    'description' => 'NFPA 1006 certification for non-entry and entry rescue from permit-required confined spaces, including atmospheric monitoring and ventilation.',
                ], [
                    'name' => 'Emergency Medical Technician (EMT)',
                    'description' => 'National Registry EMT-Basic certification authorizing the provision of basic life support patient care under regional protocols.',
                ], [
                    'name' => 'Paramedic',
                    'description' => 'National Registry Paramedic certification authorizing advanced life support patient care, including airway management, cardiac monitoring, and pharmacology.',
                ],
            )
            ->create();

        $ranks = Rank::factory()
            ->count(7)
            ->sequence(
                ['name' => 'Firefighter', 'abbreviation' => 'FF', 'paygrade' => 'F1', 'order' => 7],
                ['name' => 'Engineer', 'abbreviation' => 'ENG', 'paygrade' => 'F2', 'order' => 6],
                ['name' => 'Lieutenant', 'abbreviation' => 'LT', 'paygrade' => 'F3', 'order' => 5],
                ['name' => 'Captain', 'abbreviation' => 'CPT', 'paygrade' => 'F4', 'order' => 4],
                ['name' => 'Battalion Chief', 'abbreviation' => 'BC', 'paygrade' => 'F5', 'order' => 3],
                ['name' => 'Deputy Chief', 'abbreviation' => 'DPC', 'paygrade' => 'F6', 'order' => 2],
                ['name' => 'Fire Chief', 'abbreviation' => 'FC', 'paygrade' => 'F7', 'order' => 1],
            )
            ->create()
            ->each(function (Rank $rank) {
                $path = "ranks/$rank->abbreviation.svg";
                $image = storage_path("app/images/ranks/fire/$rank->abbreviation.svg");

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
            ->count(4)
            ->sequence(
                ['name' => 'Active', 'color' => '#16a34a', 'icon' => 'heroicon-o-fire'],
                ['name' => 'Light Duty', 'color' => '#facc15', 'icon' => 'heroicon-o-shield-check'],
                ['name' => 'On Leave', 'color' => '#0284c7', 'icon' => 'heroicon-o-calendar'],
                ['name' => 'Inactive', 'color' => '#dc2626', 'icon' => 'heroicon-o-x-circle'],
            )
            ->create();

        $tasks = Task::factory()
            ->count(4)
            ->sequence(
                ['title' => 'Complete Daily Apparatus Check', 'description' => 'Inspect assigned apparatus and verify all equipment is on board and serviceable.'],
                ['title' => 'Submit Patient Care Report', 'description' => 'Complete and submit all outstanding ePCRs prior to end of shift.'],
                ['title' => 'Quarterly Hose Testing', 'description' => 'Conduct annual service test on all 1.75" and 2.5" attack lines per NFPA 1962.'],
                ['title' => 'Annual SCBA Fit Testing', 'description' => 'Schedule and complete OSHA-required quantitative fit test for assigned facepiece.'],
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
                ->count(3), 'award_records')
            ->has(CombatRecord::factory()
                ->for($user, 'author')
                ->recycle($documents)
                ->count(3), 'combat_records')
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
            ->hasAttached($tasks->random(2), ['assigned_by_id' => $user->getKey(), 'assigned_at' => now()])
            ->hasAttached($events->random(3))
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
            ->state([
                'name' => 'Personnel Action Request',
                'slug' => 'personnel-action-request',
                'description' => 'Request changes to assignment, shift, classification, or other personnel actions.',
                'instructions' => "Submit at least 14 days prior to the desired effective date. Routing:\n\n1. Member completes the request below.\n2. Company Officer endorses.\n3. Battalion Chief reviews and forwards to the Operations Division.\n4. Final approval rests with the Deputy Chief of Operations.",
                'success_message' => 'Your Personnel Action Request has been submitted and routed to your Company Officer.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(7)
                ->sequence(
                    ['name' => 'Member Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'LAST, FIRST MI'],
                    ['name' => 'Employee ID', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Current Rank', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Station / Shift Assignment', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'e.g. Station 1 / A-Shift'],
                    ['name' => 'Action Type', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Shift Change', 'Permanent Trade', 'Station Reassignment', 'Status Change', 'Promotion / Demotion', 'Resignation / Retirement', 'Other'])],
                    ['name' => 'Effective Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Reason / Justification', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Vacation / Leave Request',
                'slug' => 'leave-request',
                'description' => 'Submit requests for vacation, sick, personal leave, or shift trades.',
                'instructions' => 'Vacation requests must be submitted in accordance with the collective bargaining agreement. Trade requests require the trading member\'s acknowledgement signature and may not result in overtime.',
                'success_message' => 'Leave request submitted. Do not modify your shift coverage until the request is approved.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(7)
                ->sequence(
                    ['name' => 'Member Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Employee ID', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Leave Type', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Vacation', 'Sick', 'Personal', 'Comp Time', 'Bereavement', 'FMLA', 'Shift Trade'])],
                    ['name' => 'Start Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'End Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Trading With (Member Name)', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => false, 'help' => 'Leave blank if not a shift trade.'],
                    ['name' => 'Notes', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => false],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Apparatus / Equipment Issue Report',
                'slug' => 'apparatus-issue-report',
                'description' => 'Report damaged, missing, or out-of-service apparatus and equipment so the maintenance division can take action.',
                'instructions' => 'If the issue places the apparatus out of service, immediately notify the on-duty Battalion Chief in addition to submitting this report.',
                'success_message' => 'Issue report submitted. The maintenance division will follow up within one business day.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(7)
                ->sequence(
                    ['name' => 'Reporting Member', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Apparatus / Equipment ID', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'e.g. Engine 1, SCBA #214'],
                    ['name' => 'Date Discovered', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Severity', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Cosmetic', 'Functional - In Service', 'Out of Service', 'Safety Critical'])],
                    ['name' => 'Out of Service?', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                    ['name' => 'Description of Issue', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Describe the issue, when it was discovered, and any related circumstances.'],
                    ['name' => 'Photo / Documentation', 'type' => FieldType::FIELD_FILE, 'cast' => FieldType::FIELD_FILE->getCast(), 'required' => false],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Incident Exposure Report',
                'slug' => 'exposure-report',
                'description' => 'Report bloodborne pathogen, infectious disease, or hazardous substance exposures sustained on duty.',
                'instructions' => 'Submit within 24 hours of exposure. Follow up with the department physician for medical evaluation. Confidentiality will be maintained in accordance with HIPAA and department policy.',
                'success_message' => 'Exposure report submitted. The Safety Officer and department physician have been notified.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(8)
                ->sequence(
                    ['name' => 'Member Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Employee ID', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Incident Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Date / Time of Exposure', 'type' => FieldType::FIELD_DATETIME, 'cast' => FieldType::FIELD_DATETIME->getCast(), 'required' => true],
                    ['name' => 'Type of Exposure', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Bloodborne Pathogen', 'Airborne Pathogen', 'Hazardous Material', 'Smoke / Products of Combustion', 'Sharps / Needlestick', 'Other'])],
                    ['name' => 'PPE in Use at Time of Exposure', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                    ['name' => 'Description of Incident', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                    ['name' => 'Sought Medical Evaluation?', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Recruit / Cadet Application',
                'slug' => 'recruit-application',
                'description' => 'Apply to join the department as a recruit firefighter or cadet.',
                'instructions' => 'Applicants must be at least 18 years of age, possess a valid driver\'s license, and pass the CPAT physical ability test prior to academy entry.',
                'success_message' => 'Thank you for your interest in joining our department. The recruitment division will review your application and contact you regarding next steps.',
                'is_public' => true,
            ])
            ->hasAttached(Field::factory()
                ->count(9)
                ->sequence(
                    ['name' => 'Full Legal Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Email Address', 'type' => FieldType::FIELD_EMAIL, 'cast' => FieldType::FIELD_EMAIL->getCast(), 'required' => true],
                    ['name' => 'Phone Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Date of Birth', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Current EMS Certification', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['None', 'EMR', 'EMT-Basic', 'AEMT', 'Paramedic'])],
                    ['name' => 'Currently Certified Firefighter?', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                    ['name' => 'CPAT Card on File?', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                    ['name' => 'Why do you want to join the fire service?', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                    ['name' => 'Resume / Application Packet', 'type' => FieldType::FIELD_FILE, 'cast' => FieldType::FIELD_FILE->getCast(), 'required' => false],
                )
            )
            ->create();

        $issuers = Issuer::factory()
            ->count(3)
            ->sequence(
                ['name' => 'State Fire Marshal\'s Office'],
                ['name' => 'National Registry of Emergency Medical Technicians'],
                ['name' => 'Department of Motor Vehicles'],
            )
            ->create();

        Credential::factory()
            ->count(5)
            ->recycle($issuers)
            ->sequence(
                ['name' => 'EMT - Basic', 'type' => CredentialType::Certification],
                ['name' => 'Paramedic', 'type' => CredentialType::Certification],
                ['name' => 'Hazardous Materials Technician', 'type' => CredentialType::Certification],
                ['name' => 'Commercial Driver\'s License (Class B)', 'type' => CredentialType::License],
                ['name' => 'Fire Officer I', 'type' => CredentialType::Certification],
            )
            ->create();

        Page::factory()
            ->count(3)
            ->sequence(
                [
                    'name' => 'Awards',
                    'description' => 'Public-facing page displaying department awards via the PERSCOM widgets.',
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
HTML,
                ], [
                    'name' => 'Ranks',
                    'description' => 'Public-facing page showing the department\'s rank structure.',
                    'slug' => 'ranks',
                    'hidden' => false,
                    'icon' => 'heroicon-o-chevron-double-up',
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
        script.setAttribute('data-widget', 'ranks');

        if (document.documentElement.classList.contains('dark')) {
            script.setAttribute('data-dark', 'true');
        }

        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
      }
  }" x-init="init">
    <div id="perscom_widget_wrapper"></div>
</div>
HTML,
                ], [
                    'name' => 'Qualifications',
                    'description' => 'Public-facing page showing operational and EMS qualifications.',
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
HTML,
                ],
            )
            ->create();
    }
}
