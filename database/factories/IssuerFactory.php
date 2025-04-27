<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Issuer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Issuer>
 */
class IssuerFactory extends Factory
{
    public function definition(): array
    {
        $issuer = "Issuer {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $issuer,
        ];
    }
}
