<?php

namespace Database\Seeders;

use Database\Seeders\Forms\FormSeeder;
use Database\Seeders\Records\AssignmentRecordSeeder;
use Database\Seeders\Records\AwardRecordSeeder;
use Database\Seeders\Records\CombatRecordSeeder;
use Database\Seeders\Records\QualificationRecordSeeder;
use Database\Seeders\Records\RankRecordSeeder;
use Database\Seeders\Records\ServiceRecordSeeder;
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
        	AssignmentRecordSeeder::class,
        	AwardRecordSeeder::class,
        	CombatRecordSeeder::class,
        	FormSeeder::class,
        	PermissionSeeder::class,
        	PersonSeeder::class,
	        QualificationRecordSeeder::class,
	        RankRecordSeeder::class,
	        RoleSeeder::class,
	        ServiceRecordSeeder::class,
	        UserSeeder::class,
        ]);
    }
}
