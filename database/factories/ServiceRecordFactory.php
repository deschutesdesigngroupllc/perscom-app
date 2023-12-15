<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRecord>
 */
class ServiceRecordFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'document_id' => $this->faker->randomElement([Document::factory(), null]),
            'author_id' => User::factory(),
            'text' => $this->faker->sentence(),
        ];
    }
}
