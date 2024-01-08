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

class FireServiceSeeder extends Seeder
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
                'title' => 'Welcome to the PERSCOM Fire Service Demo',
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
                ['name' => 'Field 1', 'type' => Field::$fieldTypes[Field::FIELD_TEXT], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_TEXT], 'cast' => Field::$fieldCasts[Field::FIELD_TEXT]],
                ['name' => 'Field 2', 'type' => Field::$fieldTypes[Field::FIELD_BOOLEAN], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_BOOLEAN], 'cast' => Field::$fieldCasts[Field::FIELD_BOOLEAN]],
                ['name' => 'Field 3', 'type' => Field::$fieldTypes[Field::FIELD_DATE], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_DATE], 'cast' => Field::$fieldCasts[Field::FIELD_DATE]],
                ['name' => 'Field 4', 'type' => Field::$fieldTypes[Field::FIELD_EMAIL], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_EMAIL], 'cast' => Field::$fieldCasts[Field::FIELD_EMAIL]],
                ['name' => 'Field 5', 'type' => Field::$fieldTypes[Field::FIELD_TIMEZONE], 'nova_type' => Field::$novaFieldTypes[Field::FIELD_TIMEZONE], 'cast' => Field::$fieldCasts[Field::FIELD_TIMEZONE]],
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
            ->hasAttached($positions->take(1), [], 'secondary_positions')
            ->hasAttached($specialties->take(1), [], 'secondary_specialties')
            ->hasAttached($units->take(1), [], 'secondary_units')
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
