<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => "Announcement {$this->faker->unique()->randomNumber()}",
            'content' => $this->faker->paragraph,
            'color' => $this->faker->hexColor,
            'global' => $this->faker->boolean,
            'expires_at' => $this->faker->dateTimeBetween('now', '21 days'),
        ];
    }
}
