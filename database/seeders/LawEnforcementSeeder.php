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

class LawEnforcementSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        Announcement::factory()
            ->state([
                'title' => 'Welcome to the PERSCOM Law Enforcement Demo',
                'content' => 'Use this demo to explore how PERSCOM streamlines roster management, training records, and personnel actions for police, sheriff, and other law enforcement agencies. For questions, contact support@deschutesdesigngroup.com.',
                'color' => '#1d4ed8',
                'global' => true,
            ])
            ->create();

        Announcement::factory()
            ->state([
                'title' => 'Annual Firearms Re-Qualification - Window Open',
                'content' => 'The annual firearms re-qualification window is open for the next 30 days. All sworn personnel must complete day and low-light qualifications with their primary duty weapon prior to the deadline.',
                'color' => '#facc15',
                'global' => true,
            ])
            ->create();

        $documents = Document::factory()
            ->count(7)
            ->recycle($user)
            ->sequence(
                ['name' => 'General Order 100 - Code of Conduct', 'description' => 'Department-wide standards of conduct, ethics, and professional behavior expected of all sworn and civilian personnel.'],
                ['name' => 'General Order 200 - Use of Force Policy', 'description' => 'Authorized levels of force, de-escalation requirements, reporting obligations, and review procedures consistent with Graham v. Connor and department policy.'],
                ['name' => 'General Order 215 - Vehicle Pursuit Policy', 'description' => 'Criteria for initiating, continuing, and terminating vehicle pursuits, including supervisor responsibilities and pursuit intervention techniques.'],
                ['name' => 'General Order 305 - Body-Worn Camera', 'description' => 'BWC activation, deactivation, retention, and review requirements, including provisions for critical incidents and evidentiary preservation.'],
                ['name' => 'General Order 410 - Critical Incident Response', 'description' => 'Standardized response procedures for active threat, barricaded subject, hostage, and mass casualty events, including unified command coordination.'],
                ['name' => 'Field Training Program Manual', 'description' => 'Phased Field Training Officer (FTO) program, including Daily Observation Reports, performance benchmarks, and certification requirements.'],
                ['name' => 'Patrol Operations Handbook', 'description' => 'Day-to-day patrol procedures including shift briefings, beat assignments, traffic stops, and report writing standards.'],
            )
            ->create();

        $positions = Position::factory()
            ->count(9)
            ->sequence(
                ['name' => 'Chief of Police', 'order' => 1],
                ['name' => 'Deputy Chief', 'order' => 2],
                ['name' => 'Division Commander', 'order' => 3],
                ['name' => 'Watch Commander', 'order' => 4],
                ['name' => 'Patrol Sergeant', 'order' => 5],
                ['name' => 'Field Training Officer', 'order' => 6],
                ['name' => 'Patrol Officer', 'order' => 7],
                ['name' => 'Detective', 'order' => 8],
                ['name' => 'K9 Handler', 'order' => 9],
            )
            ->create();

        $specialties = Specialty::factory()
            ->count(8)
            ->sequence(
                ['name' => 'Special Weapons and Tactics', 'abbreviation' => 'SWAT', 'description' => 'Tactical team trained in high-risk warrant service, hostage rescue, barricaded subjects, and active threat response.', 'order' => 1],
                ['name' => 'Crisis Negotiation Team', 'abbreviation' => 'CNT', 'description' => 'Specialty team trained to communicate with subjects in crisis, hostage takers, and barricaded individuals to achieve peaceful resolution.', 'order' => 2],
                ['name' => 'K9 Unit', 'abbreviation' => 'K9', 'description' => 'Handlers and canines certified for patrol, narcotics, explosives detection, or tracking and apprehension operations.', 'order' => 3],
                ['name' => 'Criminal Investigations', 'abbreviation' => 'CID', 'description' => 'Plain-clothes investigators assigned to follow-up investigations of person and property crimes, narcotics, and major cases.', 'order' => 4],
                ['name' => 'Traffic Enforcement Unit', 'abbreviation' => 'TEU', 'description' => 'Specialized traffic enforcement, crash reconstruction, and DUI interdiction.', 'order' => 5],
                ['name' => 'School Resource Officer', 'abbreviation' => 'SRO', 'description' => 'Sworn officers assigned to public school campuses for safety, mentorship, and law enforcement liaison duties.', 'order' => 6],
                ['name' => 'Marine Patrol', 'abbreviation' => 'MAR', 'description' => 'Waterborne patrol and rescue operations, including boating safety enforcement and dive recovery support.', 'order' => 7],
                ['name' => 'Drone / sUAS Operator', 'abbreviation' => 'sUAS', 'description' => 'FAA Part 107 certified operators flying small unmanned aircraft systems for tactical, search, and crash reconstruction support.', 'order' => 8],
            )
            ->create();

        $units = Unit::factory()
            ->count(6)
            ->hasAttached(Slot::factory()
                ->sequence(
                    ['name' => 'Patrol Sergeant', 'specialty_id' => null, 'position_id' => 5, 'order' => 1],
                    ['name' => 'Field Training Officer', 'specialty_id' => null, 'position_id' => 6, 'order' => 2],
                    ['name' => 'Patrol Officer', 'specialty_id' => null, 'position_id' => 7, 'order' => 3],
                    ['name' => 'Patrol Officer', 'specialty_id' => null, 'position_id' => 7, 'order' => 4],
                    ['name' => 'Patrol Officer', 'specialty_id' => null, 'position_id' => 7, 'order' => 5],
                )
                ->count(2)
            )
            ->sequence(
                ['name' => 'Office of the Chief', 'order' => 1],
                ['name' => 'Patrol Division - A Squad', 'order' => 2],
                ['name' => 'Patrol Division - B Squad', 'order' => 3],
                ['name' => 'Patrol Division - C Squad', 'order' => 4],
                ['name' => 'Criminal Investigations Division', 'order' => 5],
                ['name' => 'Special Operations Division', 'order' => 6],
            )
            ->create();

        Group::factory()
            ->state([
                'name' => 'Operations',
                'icon' => 'heroicon-o-shield-check',
                'order' => 1,
            ])
            ->hasAttached($units)
            ->create();

        Group::factory()
            ->state([
                'name' => 'Training & Professional Standards',
                'icon' => 'heroicon-o-academic-cap',
                'order' => 2,
            ])
            ->hasAttached(Unit::factory()
                ->state([
                    'name' => 'Training Bureau',
                    'order' => 7,
                ])
                ->hasAttached(Slot::factory()
                    ->state([
                        'name' => 'Training Sergeant',
                        'empty' => 'Position currently open.',
                    ])
                )
            )
            ->create();

        $awards = Award::factory()
            ->count(8)
            ->sequence(
                [
                    'name' => 'Medal of Honor',
                    'description' => 'The department\'s highest honor, awarded for an act of conspicuous bravery performed at imminent and personal risk of life with full knowledge of the risks involved.',
                ], [
                    'name' => 'Medal of Valor',
                    'description' => 'Awarded for an act of bravery performed in the line of duty at significant personal risk that goes beyond the expected scope of duty.',
                ], [
                    'name' => 'Purple Heart',
                    'description' => 'Awarded to officers who sustain serious physical injury as a result of a felonious, unlawful act committed against them in the line of duty.',
                ], [
                    'name' => 'Lifesaving Medal',
                    'description' => 'Awarded to officers whose direct and decisive actions are responsible for saving a human life, including CPR, water rescue, or applied tourniquet care.',
                ], [
                    'name' => 'Distinguished Service Medal',
                    'description' => 'Awarded for sustained meritorious service or a single act of significant achievement that brings substantial credit to the department.',
                ], [
                    'name' => 'Meritorious Service Award',
                    'description' => 'Recognizes exceptional performance of duty in a specific incident or assignment that exceeds normally expected service.',
                ], [
                    'name' => 'Community Policing Award',
                    'description' => 'Awarded to officers whose sustained engagement, problem-solving, and partnership with community members produces measurable improvements in public safety.',
                ], [
                    'name' => 'Officer of the Year',
                    'description' => 'Annual department honor recognizing the sworn member whose overall performance, professionalism, and leadership best represent the agency throughout the calendar year.',
                ],
            )
            ->create();

        $calendars = Calendar::factory()
            ->count(4)
            ->sequence(
                ['name' => 'Patrol Operations', 'description' => 'Shift briefings, special details, and operational events.', 'color' => '#1d4ed8'],
                ['name' => 'Training', 'description' => 'In-service training, range days, and instructor development.', 'color' => '#16a34a'],
                ['name' => 'Court / Subpoena', 'description' => 'Court appearances, grand jury, and deposition schedules.', 'color' => '#dc2626'],
                ['name' => 'Community Engagement', 'description' => 'Coffee with a Cop, citizen academy, and community outreach events.', 'color' => '#facc15'],
            )
            ->create();

        $events = Event::factory()
            ->count(8)
            ->recycle($calendars)
            ->sequence(
                ['name' => 'Annual Firearms Re-Qualification - Day Course'],
                ['name' => 'Defensive Tactics In-Service'],
                ['name' => 'Emergency Vehicle Operations Course (EVOC)'],
                ['name' => 'Crisis Intervention Team (CIT) Refresher'],
                ['name' => 'Active Threat / Solo Officer Response Drill'],
                ['name' => 'Citizen Academy - Session 4'],
                ['name' => 'Coffee with a Cop - Northside Precinct'],
                ['name' => 'Awards & Recognition Ceremony'],
            )
            ->for($user, 'author')
            ->create();

        $fields = Field::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Badge Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast()],
                ['name' => 'Sworn Officer', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast()],
                ['name' => 'Date of Hire', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast()],
                ['name' => 'Department Email', 'type' => FieldType::FIELD_EMAIL, 'cast' => FieldType::FIELD_EMAIL->getCast()],
                ['name' => 'Time Zone', 'type' => FieldType::FIELD_TIMEZONE, 'cast' => FieldType::FIELD_TIMEZONE->getCast()],
            )
            ->create();

        $qualifications = Qualification::factory()
            ->count(8)
            ->sequence(
                [
                    'name' => 'POST Basic Law Enforcement Academy',
                    'description' => 'State Peace Officer Standards and Training basic academy certification, the prerequisite for sworn law enforcement service.',
                ], [
                    'name' => 'Field Training Program (FTO)',
                    'description' => 'Successful completion of the department\'s phased FTO program, qualifying the officer for solo patrol assignment.',
                ], [
                    'name' => 'Firearms Qualification - Duty Handgun',
                    'description' => 'Annual day and low-light qualification with the assigned duty handgun, performed under the supervision of a certified firearms instructor.',
                ], [
                    'name' => 'Patrol Rifle Operator',
                    'description' => 'Department-issued patrol rifle qualification covering manipulation, zeroing, transitions, and use of force application.',
                ], [
                    'name' => 'Emergency Vehicle Operations (EVOC)',
                    'description' => 'Initial and refresher emergency vehicle operations training including pursuit driving, precision maneuvers, and response code policies.',
                ], [
                    'name' => 'Crisis Intervention Team (CIT)',
                    'description' => '40-hour Memphis Model Crisis Intervention Team training for response to mental health and behavioral health crises.',
                ], [
                    'name' => 'Taser / CEW Operator',
                    'description' => 'Annual conducted electrical weapon (CEW) operator certification including device operations, deployment guidelines, and reporting requirements.',
                ], [
                    'name' => 'SWAT Operator Course',
                    'description' => 'Selection and basic SWAT operator training in close quarters battle, dynamic entry, breaching, and team movement.',
                ],
            )
            ->create();

        $ranks = Rank::factory()
            ->count(8)
            ->sequence(
                ['name' => 'Police Officer', 'abbreviation' => 'PO', 'paygrade' => 'P1', 'order' => 8],
                ['name' => 'Detective', 'abbreviation' => 'DET', 'paygrade' => 'P2', 'order' => 7],
                ['name' => 'Corporal', 'abbreviation' => 'CPL', 'paygrade' => 'P3', 'order' => 6],
                ['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'P4', 'order' => 5],
                ['name' => 'Lieutenant', 'abbreviation' => 'LT', 'paygrade' => 'P5', 'order' => 4],
                ['name' => 'Captain', 'abbreviation' => 'CPT', 'paygrade' => 'P6', 'order' => 3],
                ['name' => 'Deputy Chief', 'abbreviation' => 'DPC', 'paygrade' => 'P7', 'order' => 2],
                ['name' => 'Chief of Police', 'abbreviation' => 'COP', 'paygrade' => 'P8', 'order' => 1],
            )
            ->create()
            ->each(function (Rank $rank) {
                $path = "ranks/$rank->abbreviation.svg";
                $image = storage_path("app/images/ranks/law/$rank->abbreviation.svg");

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
            ->count(5)
            ->sequence(
                ['name' => 'Active Duty', 'color' => '#16a34a', 'icon' => 'heroicon-o-shield-check'],
                ['name' => 'Light / Modified Duty', 'color' => '#facc15', 'icon' => 'heroicon-o-clipboard-document-check'],
                ['name' => 'Administrative Leave', 'color' => '#0284c7', 'icon' => 'heroicon-o-document-magnifying-glass'],
                ['name' => 'Field Training', 'color' => '#7c3aed', 'icon' => 'heroicon-o-academic-cap'],
                ['name' => 'Inactive', 'color' => '#dc2626', 'icon' => 'heroicon-o-x-circle'],
            )
            ->create();

        $tasks = Task::factory()
            ->count(4)
            ->sequence(
                ['title' => 'Submit Use of Force Report', 'description' => 'Complete and submit any outstanding Use of Force reports prior to end of shift.'],
                ['title' => 'Complete BWC Footage Review', 'description' => 'Review and categorize body-worn camera footage for assigned incidents.'],
                ['title' => 'Annual Firearms Re-Qualification', 'description' => 'Complete day and low-light qualification with the assigned duty weapon prior to the qualification window deadline.'],
                ['title' => 'Court Subpoena - State v. Defendant', 'description' => 'Confirm appearance, review case file, and coordinate evidence with the District Attorney\'s office.'],
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
                    'headline' => 'Recruit Class 24-02 Graduates Academy',
                    'text' => 'Twelve new officers from Recruit Class 24-02 graduated from the regional law enforcement academy this week and are reporting to Field Training. Welcome to the department.',
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
                    'headline' => 'Annual Awards Ceremony Scheduled',
                    'text' => 'The annual awards and recognition ceremony has been scheduled. Supervisors are reminded that recommendation packets are due to the Awards Committee no later than the end of the month.',
                ],
            ])
            ->for($user, 'causer')
            ->create();

        Form::factory()
            ->state([
                'name' => 'Use of Force Report',
                'slug' => 'use-of-force-report',
                'description' => 'Required documentation any time a sworn member uses force above unresisted handcuffing, displays a weapon, or deploys less-lethal or lethal options.',
                'instructions' => "Submit prior to end of shift unless the involved officer is the subject of a critical incident review. Include narrative, force factors articulation, and BWC review references.\n\nAll reports are reviewed by the chain of command and the Use of Force Review Board.",
                'success_message' => 'Use of Force Report submitted. The report has been routed to your immediate supervisor and the UoF Review Board.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(9)
                ->sequence(
                    ['name' => 'Reporting Officer', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Badge Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Incident / Case Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Date / Time of Incident', 'type' => FieldType::FIELD_DATETIME, 'cast' => FieldType::FIELD_DATETIME->getCast(), 'required' => true],
                    ['name' => 'Location', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Force Type', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Soft Empty Hand', 'Hard Empty Hand', 'OC Spray', 'Baton / Impact Weapon', 'Conducted Electrical Weapon (CEW)', 'Less-Lethal Munition', 'K9 Apprehension', 'Firearm - Display / Pointing', 'Firearm - Discharged'])],
                    ['name' => 'Subject Injury', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                    ['name' => 'Officer Injury', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                    ['name' => 'Narrative & Force Factors', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Articulate the totality of circumstances under Graham v. Connor, including subject behavior, severity of crime, and immediate threat assessment.'],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Vehicle Pursuit Report',
                'slug' => 'vehicle-pursuit-report',
                'description' => 'Required documentation following any vehicle pursuit, terminated or otherwise, in accordance with General Order 215.',
                'instructions' => 'Submit prior to end of shift. Pursuits resulting in collision, injury, or property damage will be reviewed by the Pursuit Review Board within 30 days.',
                'success_message' => 'Pursuit Report submitted. A supervisor will follow up to complete the review process.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(9)
                ->sequence(
                    ['name' => 'Initiating Officer', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Badge Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Case Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Pursuit Initiated', 'type' => FieldType::FIELD_DATETIME, 'cast' => FieldType::FIELD_DATETIME->getCast(), 'required' => true],
                    ['name' => 'Pursuit Terminated', 'type' => FieldType::FIELD_DATETIME, 'cast' => FieldType::FIELD_DATETIME->getCast(), 'required' => true],
                    ['name' => 'Reason for Pursuit', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Felony - Violent', 'Felony - Property', 'Misdemeanor', 'Stolen Vehicle', 'DUI', 'Traffic Violation Only', 'Other'])],
                    ['name' => 'Termination Reason', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Subject Stopped', 'PIT / Tactical Intervention', 'Subject Lost', 'Supervisor Termination', 'Officer Termination', 'Collision'])],
                    ['name' => 'Collision or Injury Occurred?', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                    ['name' => 'Pursuit Narrative', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Include speeds, distance, weather, road conditions, traffic, supervisor notification, and tactics employed.'],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Personnel Action Request',
                'slug' => 'personnel-action-request',
                'description' => 'Request changes to assignment, shift, classification, or other personnel actions.',
                'instructions' => "Submit at least 14 days prior to the desired effective date. Routing:\n\n1. Member completes the request below.\n2. Immediate supervisor endorses.\n3. Bureau commander reviews and forwards to the Office of the Chief.",
                'success_message' => 'Personnel Action Request submitted and routed to your immediate supervisor.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(7)
                ->sequence(
                    ['name' => 'Member Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true, 'placeholder' => 'LAST, FIRST MI'],
                    ['name' => 'Badge Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Current Rank', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Current Assignment', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Action Requested', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Shift / Watch Change', 'Beat Reassignment', 'Specialty Assignment', 'Promotion', 'Transfer', 'Resignation / Retirement', 'Other'])],
                    ['name' => 'Effective Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Reason / Justification', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Training Course Request',
                'slug' => 'training-course-request',
                'description' => 'Request approval and funding to attend an external training course, conference, or instructor development.',
                'instructions' => 'Submit at least 30 days prior to the course start date. Include course flyer, cost breakdown, and a written justification of how the training supports current and future job duties.',
                'success_message' => 'Training request submitted to the Training Bureau for review.',
                'is_public' => false,
            ])
            ->hasAttached(Field::factory()
                ->count(8)
                ->sequence(
                    ['name' => 'Requesting Member', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Badge Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Course Title', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Hosting Agency / Vendor', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Course Start Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Course End Date', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Estimated Total Cost', 'type' => FieldType::FIELD_NUMBER, 'cast' => FieldType::FIELD_NUMBER->getCast(), 'required' => true, 'help' => 'Include tuition, lodging, per diem, and travel.'],
                    ['name' => 'Justification', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true, 'help' => 'Explain how this training supports current duties, professional development, or department initiatives.'],
                )
            )
            ->create();

        Form::factory()
            ->state([
                'name' => 'Recruit / Lateral Application',
                'slug' => 'recruit-application',
                'description' => 'Apply to join the department as a recruit officer, lateral transfer, or returning sworn member.',
                'instructions' => 'Applicants must be at least 21 years of age, possess a valid driver\'s license, and be free from any disqualifying criminal history. Lateral applicants must hold a current state POST certification in good standing.',
                'success_message' => 'Thank you for your interest in joining our department. The recruitment unit will review your application and contact you regarding next steps.',
                'is_public' => true,
            ])
            ->hasAttached(Field::factory()
                ->count(9)
                ->sequence(
                    ['name' => 'Full Legal Name', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Email Address', 'type' => FieldType::FIELD_EMAIL, 'cast' => FieldType::FIELD_EMAIL->getCast(), 'required' => true],
                    ['name' => 'Phone Number', 'type' => FieldType::FIELD_TEXT, 'cast' => FieldType::FIELD_TEXT->getCast(), 'required' => true],
                    ['name' => 'Date of Birth', 'type' => FieldType::FIELD_DATE, 'cast' => FieldType::FIELD_DATE->getCast(), 'required' => true],
                    ['name' => 'Application Type', 'type' => FieldType::FIELD_SELECT, 'cast' => FieldType::FIELD_SELECT->getCast(), 'required' => true, 'options_type' => FieldOptionsType::Array, 'options' => json_encode(['Recruit (Non-Certified)', 'Lateral (Currently Certified)', 'Returning Sworn Member'])],
                    ['name' => 'Currently POST Certified?', 'type' => FieldType::FIELD_BOOLEAN, 'cast' => FieldType::FIELD_BOOLEAN->getCast(), 'required' => true],
                    ['name' => 'Prior Sworn Service', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => false, 'help' => 'List previous agencies, dates of service, and assignments. Leave blank if non-certified.'],
                    ['name' => 'Why do you want to be a police officer?', 'type' => FieldType::FIELD_TEXTAREA, 'cast' => FieldType::FIELD_TEXTAREA->getCast(), 'required' => true],
                    ['name' => 'Resume / Application Packet', 'type' => FieldType::FIELD_FILE, 'cast' => FieldType::FIELD_FILE->getCast(), 'required' => false],
                )
            )
            ->create();

        $issuers = Issuer::factory()
            ->count(3)
            ->sequence(
                ['name' => 'State Peace Officer Standards and Training (POST)'],
                ['name' => 'Federal Bureau of Investigation - National Academy'],
                ['name' => 'Department of Motor Vehicles'],
            )
            ->create();

        Credential::factory()
            ->count(5)
            ->recycle($issuers)
            ->sequence(
                ['name' => 'POST Basic Peace Officer Certification', 'type' => CredentialType::Certification],
                ['name' => 'POST Intermediate Certificate', 'type' => CredentialType::Certification],
                ['name' => 'FBI National Academy Graduate', 'type' => CredentialType::Certification],
                ['name' => 'Commercial Driver\'s License (Class A)', 'type' => CredentialType::License],
                ['name' => 'Top Secret Security Clearance', 'type' => CredentialType::Other],
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
                    'name' => 'Specialties',
                    'description' => 'Public-facing page showing department specialty units.',
                    'slug' => 'specialties',
                    'hidden' => false,
                    'icon' => 'heroicon-o-briefcase',
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
        script.setAttribute('data-widget', 'specialties');

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
