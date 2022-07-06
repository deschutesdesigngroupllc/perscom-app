<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create([
        	'name' => 'Test User',
	        'email' => 'test@deschutesdesigngroup.com'
        ]);
        $user->assignRole('Admin');

        User::factory()->count(9)->create();
    }
}
