<?php

namespace Database\Seeders\Records;

use App\Models\Records\Assignment;
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
        Assignment::factory()->count(10)->create();
    }
}
