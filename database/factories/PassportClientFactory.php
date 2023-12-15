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
    protected $model = PassportClient::class;

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
            'type' => $this->faker->randomElement(['authorization_code', 'implicit', 'client_credentials', 'password']),
            'secret' => Str::random(32),
            'provider' => null,
            'redirect' => $this->faker->url,
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ];
    }

    public function personalAccessClient(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
                'name' => 'Default Personal Access Client',
                'personal_access_client' => true,
                'password_client' => false,
            ];
        });
    }

    public function passwordGrantClient(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
                'name' => 'Default Password Grant Client',
                'personal_access_client' => false,
                'password_client' => true,
            ];
        });
    }
}
