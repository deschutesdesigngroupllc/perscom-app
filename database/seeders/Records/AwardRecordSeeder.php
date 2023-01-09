<?php

namespace Database\Seeders\Records;

use App\Models\Records\Award;
use Illuminate\Database\Seeder;

class AwardRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Award::withoutEvents(function () {
            Award::factory()->count(10)->create();
        });
    }
}
