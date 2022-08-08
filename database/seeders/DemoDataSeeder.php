<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Forms\FormSeeder;
use Database\Seeders\Records\AssignmentRecordSeeder;
use Database\Seeders\Records\AwardRecordSeeder;
use Database\Seeders\Records\CombatRecordSeeder;
use Database\Seeders\Records\QualificationRecordSeeder;
use Database\Seeders\Records\RankRecordSeeder;
use Database\Seeders\Records\ServiceRecordSeeder;
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
	    	AnnouncementSeeder::class,
		    AssignmentRecordSeeder::class,
		    AwardRecordSeeder::class,
		    CombatRecordSeeder::class,
		    FieldSeeder::class,
		    FormSeeder::class,
		    PermissionSeeder::class,
		    QualificationRecordSeeder::class,
		    RankRecordSeeder::class,
		    RoleSeeder::class,
		    ServiceRecordSeeder::class
	    ]);

	    $user = User::factory()->create([
		    'name' => 'Demo User',
		    'email' => 'demo@perscom.io'
	    ]);
	    $user->assignRole('Admin');
    }
}
