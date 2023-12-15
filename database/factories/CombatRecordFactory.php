<?php

namespace Database\Factories;

use App\Models\CombatRecord;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CombatRecord>
 */
class CombatRecordFactory extends Factory
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
