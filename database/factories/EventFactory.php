<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Calendar;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\Schedule;
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
            'notifications_enabled' => false,
            'registration_enabled' => true,
        ];
    }

    public function withSchedule(): static
    {
        return $this->state(function () {
            return [
                'repeats' => true,
                'all_day' => false,
            ];
        })->has(Schedule::factory());
    }

    public function withNotifications(): static
    {
        return $this->state(function () {
            return [
                'notifications_enabled' => true,
                'notifications_interval' => [NotificationInterval::PT1H],
                'notifications_channels' => [NotificationChannel::MAIL],
            ];
        });
    }

    public function withRegistrations(): static
    {
        return $this->state(function () {
            return [
                'registration_enabled' => true,
                'registration_deadline' => null,
            ];
        })->has(User::factory(), 'registrations');
    }
}
