<?php

namespace Database\Factories;

use App\Models\PassportClient;
use App\Models\PassportToken;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * @extends Factory<PassportToken>
 */
class PassportTokenFactory extends Factory
{
    protected $model = PassportToken::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::random(80),
            'user_id' => User::factory(),
            'client_id' => PassportClient::factory(),
            'name' => $this->faker->word,
            'scopes' => ['*'],
            'token' => Crypt::encryptString(JWT::encode([
                'sub' => 1,
                'scope' => ['*'],
            ], env('JWT_SECRET', Str::random(40)), 'HS256')),
            'revoked' => false,
        ];
    }
}
