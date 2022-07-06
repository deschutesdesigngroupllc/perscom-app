<?php

namespace Database\Seeders;

use Database\Seeders\Forms\FormSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
        	AwardSeeder::class,
        	DocumentSeeder::class,
        	FormSeeder::class,
        	PermissionSeeder::class,
        	PersonSeeder::class,
        	PositionSeeder::class,
	        QualificationSeeder::class,
	        RankSeeder::class,
	        RoleSeeder::class,
	        SpecialtySeeder::class,
	        StatusSeeder::class,
	        UnitSeeder::class,
	        UserSeeder::class,
        ]);
    }
}
