<?php

namespace Database\Seeders;

use App\Models\RankRecord;
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
        RankRecord::factory()->count(10)->create();
    }
}
