<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Newsfeed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Newsfeed>
 */
class NewsfeedFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->text(),
            'created_at' => now(),
            'batch_uuid' => $this->faker->uuid(),
            'subject_id' => $this->faker->randomNumber(),
            'causer_type' => $this->faker->word(),
            'updated_at' => now(),
            'subject_type' => $this->faker->word(),
            'log_name' => 'newsfeed',
            'causer_id' => $this->faker->randomNumber(),
            'properties' => $this->faker->word(),
            'event' => $this->faker->word(),
        ];
    }
}
