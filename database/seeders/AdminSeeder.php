<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = Admin::factory()->create([
        	'name' => 'Test Admin',
	        'email' => 'test@deschutesdesigngroup.com'
        ]);
    }
}
