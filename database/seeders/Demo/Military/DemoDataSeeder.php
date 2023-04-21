<?php

namespace Database\Seeders\Demo\Military;

use App\Contracts\Passport\CreatesPersonalAccessToken;
use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\AnnouncementSeeder;
use Database\Seeders\PassportSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Queue;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Queue::fake();

        $this->call([
            PassportSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            AnnouncementSeeder::class,
            AwardSeeder::class,
            FormSeeder::class,
            PositionSeeder::class,
            QualificationSeeder::class,
            RankSeeder::class,
            SpecialtySeeder::class,
            StatusSeeder::class,
            TaskSeeder::class,
            UnitSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@perscom.io',
        ]);
        $user->assignRole('Admin');

        $createApiKey = app(CreatesPersonalAccessToken::class);
        $createApiKey->create($user, 'Demo API Key', ['*']);

        Unit::all()->each(static function ($unit) {
            User::factory()->count(5)->create()->each(static function (User $user) use ($unit) {
                $user->statuses()->attach(Status::all()->random());

                AssignmentRecord::factory()
                    ->for($user)
                    ->for($unit)
                    ->state(new Sequence(function ($sequence) use ($user) {
                        return [
                            'position_id' => \App\Models\Position::all()->random(),
                            'specialty_id' => \App\Models\Specialty::all()->random(),
                            'author_id' => $user,
                        ];
                    }))
                    ->create();

                RankRecord::factory()->for($user)->state(new Sequence(function ($sequence) use ($user) {
                    return [
                        'rank_id' => \App\Models\Rank::all()->random(),
                        'author_id' => $user,
                    ];
                }))->create();

                AwardRecord::factory()->count(5)->for($user)->state(new Sequence(function ($sequence) use ($user) {
                    return [
                        'award_id' => \App\Models\Award::all()->random(),
                        'author_id' => $user,
                    ];
                }))->create();

                QualificationRecord::factory()
                    ->count(5)
                    ->for($user)
                    ->state(new Sequence(function ($sequence) use ($user) {
                        return [
                            'qualification_id' => \App\Models\Qualification::all()->random(),
                            'author_id' => $user,
                        ];
                    }))
                    ->create();

                ServiceRecord::factory()->count(5)->for($user)->state([
                    'author_id' => $user,
                ])->create();

                CombatRecord::factory()->count(5)->for($user)->state([
                    'author_id' => $user,
                ])->create();
            });
        });
    }
}
