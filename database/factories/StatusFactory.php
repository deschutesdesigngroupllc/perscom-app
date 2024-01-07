<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = "Status  {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $status,
            'text_color' => $this->faker->hexColor,
            'bg_color' => $this->faker->hexColor,
        ];
    }
}
