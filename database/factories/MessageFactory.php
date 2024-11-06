<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message' => $this->faker->sentence,
            'channels' => $this->faker->randomElements(NotificationChannel::cases()),
            'recipients' => ['*'],
        ];
    }

    public function withSchedule(): static
    {
        return $this->state(function () {
            return [
                'repeats' => true,
            ];
        })->has(Schedule::factory());
    }
}
