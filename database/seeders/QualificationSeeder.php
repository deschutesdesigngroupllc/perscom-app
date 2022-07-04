<?php

namespace Database\Seeders;

use App\Models\Qualification;
use App\Models\Rank;
use Illuminate\Database\Seeder;

class QualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Qualification::factory()->count(10)->create();
    }
}
