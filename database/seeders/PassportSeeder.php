<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PassportClient;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class PassportSeeder extends Seeder
{
    public function run(): void
    {
        $client = resolve(ClientRepository::class);
        $client->createPasswordGrantClient(PassportClient::SYSTEM_PASSWORD_GRANT_CLIENT);
        $client->createPersonalAccessGrantClient(PassportClient::SYSTEM_PERSONAL_ACCESS_CLIENT);
    }
}
