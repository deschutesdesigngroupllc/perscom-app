<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ApiKeySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $token = $user->createToken('Test Key', ['*']);
        $token->token->forceFill([
            'token' => $token->accessToken,
        ])->save();
    }
}
