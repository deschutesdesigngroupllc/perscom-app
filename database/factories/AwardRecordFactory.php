<?php

namespace Database\Factories;

use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AwardRecord>
 */
class AwardRecordFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'award_id' => Award::factory(),
            'document_id' => $this->faker->randomElement([Document::factory(), null]),
            'author_id' => User::factory(),
            'text' => $this->faker->sentence(),
        ];
    }
}
