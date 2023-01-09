<?php

namespace Database\Seeders\Records;

use App\Models\Records\Rank;
use Illuminate\Database\Seeder;

class RankRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rank::withoutEvents(function () {
            Rank::factory()->count(10)->create();
        });
    }
}
