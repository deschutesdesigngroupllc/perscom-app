<?php

namespace Database\Factories;

use App\Models\PassportClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PassportClient>
 */
class PassportClientFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'user_id' => User::factory(),
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'secret' => Str::random(32),
            'provider' => null,
            'redirect' => $this->faker->url,
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
