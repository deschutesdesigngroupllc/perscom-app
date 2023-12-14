<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Calendar;
use App\Models\CombatRecord;
use App\Models\Document;
use App\Models\Event;
use App\Models\Group;
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

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            //AssignmentRecordSeeder::class,
            FieldSeeder::class,
            FormSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@deschutesdesigngroup.com',
        ]);
        $user->assignRole('Admin');

        Announcement::factory()
            ->count(5)
            ->sequence(fn (Sequence $sequence) => ['title' => "Announcement $sequence->index"])
            ->create();

        Document::factory()
            ->count(5)
            ->sequence(fn (Sequence $sequence) => ['name' => "Document $sequence->index"])
            ->create();

        Group::factory()
            ->count(2)
            ->sequence(fn (Sequence $sequence) => ['name' => "Group $sequence->index"])
            ->has(Unit::factory()
                ->count(3)
                ->sequence(fn (Sequence $sequence) => ['name' => "Unit $sequence->index"]))
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

        $users = User::factory()
            ->count(10)
            ->recycle($positions)
            ->recycle($specialties)
            ->recycle($ranks)
            ->recycle($statuses)
            ->recycle($tasks)
            ->for(Unit::all()->random(1)->first())
            ->has(AwardRecord::factory()->for($user, 'author')->recycle($awards)->count(5), 'award_records')
            ->has(CombatRecord::factory()->for($user, 'author')->count(5), 'combat_records')
            ->has(QualificationRecord::factory()->for($user, 'author')->recycle($qualifications)->count(5), 'combat_records')
            ->has(RankRecord::factory()->for($user, 'author')->recycle($ranks)->count(5), 'rank_records')
            ->has(ServiceRecord::factory()->for($user, 'author')->count(5), 'service_records')
            ->hasAttached($tasks->random(3), ['assigned_by_id' => $user->getKey(), 'assigned_at' => now()])
            ->hasAttached($events->random(3))
            ->create();
    }
}
