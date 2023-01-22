<?php

namespace Database\Seeders\Records;

use App\Models\Records\Service;
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
        Service::withoutEvents(function () {
            Service::factory()->count(10)->create();
        });
    }
}
