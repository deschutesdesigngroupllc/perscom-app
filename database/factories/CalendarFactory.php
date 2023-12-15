<?php

namespace Database\Factories;

use App\Models\Calendar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Calendar>
 */
class CalendarFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => "Calendar {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
            'color' => $this->faker->hexColor,
        ];
    }
}
