<?php

namespace Database\Seeders;

use App\Models\CombatRecord;
use Illuminate\Database\Seeder;

class CombatRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CombatRecord::withoutEvents(function () {
            CombatRecord::factory()->count(10)->create();
        });
    }
}
