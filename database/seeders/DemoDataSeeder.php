<?php

namespace Database\Seeders;

use App\Models\Records\Assignment;
use App\Models\Records\Award;
use App\Models\Records\Combat;
use App\Models\Records\Qualification;
use App\Models\Records\Rank;
use App\Models\Records\Service;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\Forms\FormSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PassportSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            AnnouncementSeeder::class,
            AwardSeeder::class,
            FieldSeeder::class,
            FormSeeder::class,
            PositionSeeder::class,
            QualificationSeeder::class,
            RankSeeder::class,
            SpecialtySeeder::class,
            StatusSeeder::class,
            UnitSeeder::class,
        ]);

        $user = User::factory()->createQuietly([
            'name' => 'Demo User',
            'email' => 'demo@perscom.io',
        ]);
        $user->assignRole('Admin');

        Unit::all()->each(static function ($unit) {
            User::factory()->count(5)->createQuietly()->each(static function (User $user) use ($unit) {
                $user->statuses()->attach(Status::all()->random());

                Assignment::factory()->for($user)->for($unit)->state(new Sequence(function ($sequence) use ($user) {
                    return [
                        'position_id' => \App\Models\Position::all()->random(),
                        'specialty_id' => \App\Models\Specialty::all()->random(),
                        'author_id' => $user,
                    ];
                }))->createQuietly();

                Rank::factory()->for($user)->state(new Sequence(function ($sequence) use ($user) {
                    return [
                        'rank_id' => \App\Models\Rank::all()->random(),
                        'author_id' => $user,
                    ];
                }))->createQuietly();

                Award::factory()->count(5)->for($user)->state(new Sequence(function ($sequence) use ($user) {
                    return [
                        'award_id' => \App\Models\Award::all()->random(),
                        'author_id' => $user,
                    ];
                }))->createQuietly();

                Qualification::factory()->count(5)->for($user)->state(new Sequence(function ($sequence) use ($user) {
                    return [
                        'qualification_id' => \App\Models\Qualification::all()->random(),
                        'author_id' => $user,
                    ];
                }))->createQuietly();

                Service::factory()->count(5)->for($user)->state([
                    'author_id' => $user,
                ])->createQuietly();

                Combat::factory()->count(5)->for($user)->state([
                    'author_id' => $user,
                ])->createQuietly();
            });
        });
    }
}
