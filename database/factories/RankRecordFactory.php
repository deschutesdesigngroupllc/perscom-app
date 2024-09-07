<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\Enums\RankRecordType;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RankRecord>
 */
class RankRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'rank_id' => Rank::factory(),
            'document_id' => $this->faker->randomElement([Document::factory(), null]),
            'author_id' => User::factory(),
            'text' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(RankRecordType::cases()),
        ];
    }
}
