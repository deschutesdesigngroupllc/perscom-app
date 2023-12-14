<?php

namespace Database\Factories;

use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->sentence(),
            'type' => $this->faker->randomElement([RankRecord::RECORD_RANK_PROMOTION, RankRecord::RECORD_RANK_DEMOTION]),
            'author_id' => User::factory(),
            'user_id' => User::factory(),
            'rank_id' => Rank::factory(),
        ];
    }
}
