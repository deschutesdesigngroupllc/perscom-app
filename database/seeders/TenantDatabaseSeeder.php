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
use App\Models\PassportClient;
use App\Models\PassportToken;
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

class TenantDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@deschutesdesigngroup.com',
        ]);
        $user->assignRole('Admin');

        Announcement::factory()
            ->count(2)
            ->sequence(fn (Sequence $sequence) => ['title' => "Announcement $sequence->index"])
            ->create();

        $documents = Document::factory()
            ->count(5)
            ->sequence(fn (Sequence $sequence) => ['name' => "Document $sequence->index"])
            ->create();

        $units = Unit::factory()
            ->count(3)
            ->sequence(fn (Sequence $sequence) => ['name' => "Unit $sequence->index"])
            ->create();

        Group::factory()
            ->sequence(fn (Sequence $sequence) => ['name' => "Group $sequence->index"])
            ->hasAttached($units)
            ->create();

        $awards = Award::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence) => ['name' => "Award $sequence->index"])
            ->create();

        $calendars = Calendar::factory()
            ->count(5)
            ->sequence(fn (Sequence $sequence) => ['name' => "Calendar $sequence->index"])
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
            ->count(10)
            ->sequence(fn (Sequence $sequence) => ['name' => "Qualification $sequence->index"])
            ->create();

        $ranks = Rank::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence) => ['name' => "Rank $sequence->index"])
            ->create();

        $specialties = Specialty::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence) => ['name' => "Specialty $sequence->index"])
            ->create();

        $statuses = Status::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence) => ['name' => "Status $sequence->index"])
            ->create();

        $positions = Position::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence) => ['name' => "Position $sequence->index"])
            ->create();

        $tasks = Task::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence) => ['title' => "Task $sequence->index"])
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
            ->hasAttached($fields->take(3))
            ->create();

        Form::factory()
            ->count(3)
            ->sequence(fn (Sequence $sequence) => ['name' => "Form $sequence->index"])
            ->hasAttached($fields->random(3))
            ->create();

        PassportToken::factory()
            ->for($user, 'user')
            ->for(PassportClient::query()->where('name', '=', 'Default Personal Access Client')->first(), 'client')
            ->state([
                'name' => 'Default API Key',
            ])
            ->create();
    }
}
