<?php

namespace Database\Factories\Records;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankFactory extends Factory
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
	        'type' => $this->faker->boolean(),
	        'author_id' => User::factory(),
	        'user_id' => User::factory(),
	        'rank_id' => Rank::factory()
        ];
    }
}
