<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new ClientRepository();
        $client->createPasswordGrantClient(null, 'Default Password Grant Client', 'http://your.redirect.path');
        $client->createPersonalAccessClient(null, 'Default Personal Access Client', 'http://your.redirect.path');
    }
}
