<?php

namespace Database\Seeders\Records;

use App\Models\Records\Qualification;
use Illuminate\Database\Seeder;

class QualificationRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Qualification::withoutEvents(function () {
            Qualification::factory()->count(10)->create();
        });
    }
}
