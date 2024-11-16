<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enums\PassportClientType;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class PassportSeeder extends Seeder
{
    public function run(): void
    {
        $client = new ClientRepository;

        $passwordClient = $client->createPasswordGrantClient(null, 'Default Password Grant Client', 'http://your.redirect.path');
        $passwordClient->forceFill([
            'type' => PassportClientType::PASSWORD,
        ])->save();

        $client->createPersonalAccessClient(null, 'Default Personal Access Client', 'http://your.redirect.path');
    }
}
