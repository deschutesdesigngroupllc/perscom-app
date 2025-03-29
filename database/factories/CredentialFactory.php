<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Credential;
use App\Models\Enums\CredentialType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Credential>
 */
class CredentialFactory extends Factory
{
    public function definition(): array
    {
        $credential = "Credential {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $credential,
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(CredentialType::cases()),
        ];
    }
}
