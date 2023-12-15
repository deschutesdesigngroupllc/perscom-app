<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => "Announcement {$this->faker->unique()->randomNumber()}",
            'content' => $this->faker->paragraph,
            'color' => $this->faker->randomElement(['info', 'success', 'warning', 'danger']),
            'expires_at' => $this->faker->dateTimeBetween('now', '21 days'),
        ];
    }
}
