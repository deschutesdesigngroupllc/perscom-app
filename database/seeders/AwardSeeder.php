<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\Position;
use Illuminate\Database\Seeder;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Award::factory()->count(10)->create();
    }
}
