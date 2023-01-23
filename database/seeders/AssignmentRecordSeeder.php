<?php

namespace Database\Seders;

use App\Models\AssignmentRecord;
use Illuminate\Database\Seeder;

class AssignmentRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AssignmentRecord::withoutEvents(function () {
            AssignmentRecord::factory()->count(10)->create();
        });
    }
}
