<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Metric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Metric>
 */
class MetricFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => $this->faker->word,
            'count' => $this->faker->randomDigitNotNull,
        ];
    }
}
