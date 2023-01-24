<?php

namespace Database\Seeders;

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
            UserSeeder::class,
        ]);
    }
}
