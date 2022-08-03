<?php

namespace Database\Seeders\Records;

use App\Models\Records\Combat;
use Illuminate\Database\Seeder;

class CombatRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Combat::withoutEvents(function () {
		    Combat::factory()->count(10)->create();
	    });
    }
}
