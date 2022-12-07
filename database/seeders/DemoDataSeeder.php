<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Records\Assignment;
use App\Models\Records\Award;
use App\Models\Records\Combat;
use App\Models\Records\Qualification;
use App\Models\Records\Rank;
use App\Models\Records\Service;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\Forms\FormSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
	        StatusSeeder::class
        ]);

        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@perscom.io',
        ]);
        $user->assignRole('Admin');

        Unit::factory()->count(5)->create()->each(static function ($unit) {
            User::factory()->count(10)->hasAttached(Status::all()->random())->create()->each(static function ($user) use ($unit) {
                Assignment::factory()
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

                Rank::factory()
                    ->for($user)
                    ->state(new Sequence(function ($sequence) use ($user) {
                        return [
                            'rank_id' => \App\Models\Rank::all()->random(),
                            'author_id' => $user,
                        ];
                    }))
                    ->create();

	            Award::factory()
		            ->count(5)
		            ->for($user)
		            ->state(new Sequence(function ($sequence) use ($user) {
			            return [
				            'award_id' => \App\Models\Award::all()->random(),
				            'author_id' => $user,
			            ];
		            }))
		            ->create();

	            Qualification::factory()
		            ->count(5)
		            ->for($user)
		            ->state(new Sequence(function ($sequence) use ($user) {
			            return [
				            'qualification_id' => \App\Models\Qualification::all()->random(),
				            'author_id' => $user,
			            ];
		            }))
		            ->create();

	            Service::factory()
		            ->count(5)
		            ->for($user)
		            ->state([
		            	'author_id' => $user
		            ])
		            ->create();

	            Combat::factory()
		            ->count(5)
		            ->for($user)
		            ->state([
			            'author_id' => $user
		            ])
		            ->create();
            });
        });
    }
}
