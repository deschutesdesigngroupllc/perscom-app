<?php

namespace Database\Factories;

use App\Models\Calendar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::parse($this->faker->dateTimeBetween(now(), now()->endOfMonth()))->roundHour();

        return [
            'name' => "Event {$this->faker->unique()->randomNumber()}",
            'calendar_id' => Calendar::factory(),
            'description' => $this->faker->paragraph,
            'content' => $this->faker->paragraph,
            'location' => $this->faker->address,
            'url' => $this->faker->url,
            'author_id' => User::factory(),
            'all_day' => $this->faker->boolean,
            'start' => $start,
            'end' => $start->addHour(),
            'repeats' => false,
            'registration_enabled' => true,
        ];
    }
}
