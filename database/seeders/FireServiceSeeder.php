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
use App\Models\Document;
use App\Models\Enums\FieldType;
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
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class FireServiceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /** @var SetupTenantAccount $action */
        $action = app(SetupTenantAccount::class);
        $action->shouldCreateUser(false)->handle(tenant());

        $user = User::factory()->unassigned()->createQuietly([
            'name' => 'Demo User',
            'email' => 'demo@perscom.io',
        ]);
        $user->assignRole(Utils::getSuperAdminName());

        Announcement::factory()
            ->state([
                'title' => 'Welcome to the PERSCOM Fire Service Demo',
                'content' => 'This is an example announcement that can be displayed to keep your entire organization up-to-date.',
                'color' => 'danger',
                'global' => true,
            ])
            ->create();

        $documents = Document::factory()
            ->count(5)
            ->sequence(fn (Sequence $sequence) => ['name' => "Document $sequence->index"])
            ->create();

        $units = Unit::factory()
            ->count(8)
            ->sequence(
                ['name' => 'District 1'],
                ['name' => 'Battalion 1'],
                ['name' => 'Station 1'],
                ['name' => 'Station 2'],
                ['name' => 'Station 3'],
                ['name' => 'Battalion 2'],
                ['name' => 'Station 4'],
                ['name' => 'Staiton 5'],
            )
            ->create();

        Group::factory()
            ->state([
                'name' => 'Operations',
            ])
            ->hasAttached($units)
            ->create();

        $awards = Award::factory()
            ->count(2)
            ->sequence(
                ['name' => 'Fire Chief Commendation Medal'],
                ['name' => 'Unit Citation']
            )
            ->create();

        $calendars = Calendar::factory()
            ->count(4)
            ->sequence(
                ['name' => 'Operations'],
                ['name' => 'Training'],
                ['name' => 'Public Relations'],
                ['name' => 'Holidays']
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
            ->count(2)
            ->sequence(
                ['name' => 'Emergency Medical Technician'],
                ['name' => 'Paramedic'],
            )
            ->create();

        $ranks = Rank::factory()
            ->count(7)
            ->sequence(
                [
                    'name' => 'Firefighter',
                    'abbreviation' => 'FF',
                    'paygrade' => 'F1',
                ], [
                    'name' => 'Engineer',
                    'abbreviation' => 'ENG',
                    'paygrade' => 'F2',
                ], [
                    'name' => 'Captain',
                    'abbreviation' => 'CPT',
                    'paygrade' => 'F3',
                ], [
                    'name' => 'Battalion Chief',
                    'abbreviation' => 'BC',
                    'paygrade' => 'F4',
                ], [
                    'name' => 'District Chief',
                    'abbreviation' => 'DC',
                    'paygrade' => 'F5',
                ], [
                    'name' => 'Deputy Chief',
                    'abbreviation' => 'DC',
                    'paygrade' => 'F6',
                ], [
                    'name' => 'Fire Chief',
                    'abbreviation' => 'FC',
                    'paygrade' => 'F7',
                ],
            )
            ->create();

        $specialties = Specialty::factory()
            ->count(2)
            ->sequence(
                [
                    'name' => 'Car Seat Technician',
                    'abbreviation' => 'CST',
                ], [
                    'name' => 'Rescue Specialist',
                    'abbreviation' => 'RS',
                ],
            )
            ->create();

        $statuses = Status::factory()
            ->count(3)
            ->sequence(
                [
                    'name' => 'Active',
                    'color' => '#16a34a',
                ], [
                    'name' => 'Inactive',
                    'color' => '#dc2626',
                ], [
                    'name' => 'On Leave',
                    'color' => '#0284c7',
                ],
            )
            ->create();

        $positions = Position::factory()
            ->count(7)
            ->sequence(
                ['name' => 'Shift Commander'],
                ['name' => 'Officer'],
                ['name' => 'Driver'],
                ['name' => 'Firefighter/Irons'],
                ['name' => 'Firefighter/Nozzle'],
                ['name' => 'Firefighter/OV'],
                ['name' => 'Firefighter/Hydrant'],
            )
            ->create();

        $tasks = Task::factory()
            ->count(2)
            ->sequence(
                ['title' => 'Finish EMS Report'],
                ['title' => 'Update Personnel Information']
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
            ->count(2)
            ->sequence(
                ['name' => 'Personnel Action Request'],
                ['name' => 'Vacation Request'],
            )
            ->hasAttached($fields->random(3))
            ->create();
    }
}
