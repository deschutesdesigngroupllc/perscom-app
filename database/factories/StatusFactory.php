<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    public function definition(): array
    {
        $status = "Status  {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $status,
            'color' => $this->faker->randomElement(['#facc15', '#16a34a', '#2563eb', '#dc2626', '#4b5563']),
        ];
    }
}
