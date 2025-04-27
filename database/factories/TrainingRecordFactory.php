<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\TrainingRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainingRecord>
 */
class TrainingRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'instructor_id' => User::factory(),
            'author_id' => User::factory(),
            'document_id' => $this->faker->randomElement([Document::factory(), null]),
            'text' => $this->faker->sentence(),
        ];
    }
}
