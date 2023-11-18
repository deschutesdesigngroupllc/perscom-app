<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            PermissionSeeder::class,
            RoleSeeder::class,
            AnnouncementSeeder::class,
            AssignmentRecordSeeder::class,
            AwardRecordSeeder::class,
            CalendarSeeder::class,
            CombatRecordSeeder::class,
            EventSeeder::class,
            FieldSeeder::class,
            FormSeeder::class,
            PassportSeeder::class,
            QualificationRecordSeeder::class,
            RankRecordSeeder::class,
            ServiceRecordSeeder::class,
            StatusSeeder::class,
            TaskSeeder::class,
            UserSeeder::class,
        ]);
    }
}
