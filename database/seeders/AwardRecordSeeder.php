<?php

namespace Database\Seeders;

use App\Models\AwardRecord;
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
        AwardRecord::factory()->count(10)->create();
    }
}
