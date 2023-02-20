<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Queue;

class DatabaseSeeder extends Seeder
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
            PermissionSeeder::class,
            RoleSeeder::class,
            AnnouncementSeeder::class,
            AssignmentRecordSeeder::class,
            AwardRecordSeeder::class,
            CombatRecordSeeder::class,
            FieldSeeder::class,
            FormSeeder::class,
            PassportSeeder::class,
            QualificationRecordSeeder::class,
            RankRecordSeeder::class,
            ServiceRecordSeeder::class,
            TaskSeeder::class,
            UserSeeder::class,
        ]);
    }
}
