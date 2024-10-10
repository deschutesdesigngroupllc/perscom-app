<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = Carbon::parse($this->faker->dateTimeBetween(now(), now()->endOfMonth()))->roundHour();

        return [
            'name' => "Event {$this->faker->unique()->randomNumber()}",
            'calendar_id' => Calendar::factory(),
            'description' => $this->faker->paragraph,
            'content' => $this->faker->paragraph,
            'location' => Str::squish($this->faker->address),
            'url' => $this->faker->url,
            'author_id' => User::factory(),
            'all_day' => $this->faker->boolean,
            'starts' => $start,
            'ends' => $start->addHour(),
            'repeats' => false,
            'registration_enabled' => true,
        ];
    }

    public function withSchedule(): static
    {
        return $this->afterCreating(function (Event $event) {
            $event->forceFill([
                'all_day' => false,
            ])->save();

            $event->schedule()->create([
                'start' => now(),
                'end' => now()->addHour(),
                'repeats' => true,
                'frequency' => 'WEEKLY',
                'interval' => 1,
                'end_type' => 'after',
                'count' => 10,
                'by_day' => ['MO'],
            ]);
        });
    }
}
