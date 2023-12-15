<?php

namespace Database\Factories;

use App\Models\Newsfeed;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Newsfeed>
 */
class NewsfeedFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'batch_uuid' => $this->faker->uuid(),
            'subject_id' => $this->faker->randomNumber(),
            'causer_type' => $this->faker->word(),
            'updated_at' => Carbon::now(),
            'subject_type' => $this->faker->word(),
            'log_name' => 'newsfeed',
            'causer_id' => $this->faker->randomNumber(),
            'properties' => $this->faker->word(),
            'event' => $this->faker->word(),
        ];
    }
}
