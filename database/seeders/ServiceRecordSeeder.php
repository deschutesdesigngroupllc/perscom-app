<?php

namespace Database\Seeders;

use App\Models\ServiceRecord;
use Illuminate\Database\Seeder;

class ServiceRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ServiceRecord::factory()->count(10)->create();
    }
}
