<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => "Announcement {$this->faker->unique()->randomNumber()}",
            'content' => $this->faker->paragraph,
            'color' => $this->faker->randomElement(['info', 'success', 'warning', 'danger']),
            'expires_at' => $this->faker->dateTimeBetween('now', '21 days'),
        ];
    }
}
