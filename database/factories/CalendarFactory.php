<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Calendar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Calendar>
 */
class CalendarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => "Calendar {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
            'color' => $this->faker->randomElement(['#facc15', '#16a34a', '#2563eb', '#dc2626', '#4b5563']),
        ];
    }
}
