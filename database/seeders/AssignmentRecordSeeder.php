<?php

namespace Database\Seeders;

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
        AssignmentRecord::factory()->count(10)->create();
    }
}
