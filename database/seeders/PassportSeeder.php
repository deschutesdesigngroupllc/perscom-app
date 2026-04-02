<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enums\PassportClientType;
use App\Models\PassportClient;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class PassportSeeder extends Seeder
{
    public function run(): void
    {
        $client = new ClientRepository;

        $passwordClient = $client->createPasswordGrantClient(PassportClient::SYSTEM_PASSWORD_GRANT_CLIENT);
        $passwordClient->forceFill([
            'type' => PassportClientType::PASSWORD,
        ])->save();

        $client->createPersonalAccessGrantClient(PassportClient::SYSTEM_PERSONAL_ACCESS_CLIENT);
    }
}
