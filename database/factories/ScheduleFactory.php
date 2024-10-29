<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start' => now(),
            'duration' => 1,
            'frequency' => ScheduleFrequency::DAILY,
            'interval' => 1,
            'end_type' => ScheduleEndType::NEVER,
        ];
    }
}
