<?php

namespace Database\Factories;

use App\Models\Award;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AwardRecordFactory extends Factory
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
            'author_id' => User::factory(),
            'user_id' => User::factory(),
            'award_id' => Award::factory(),
        ];
    }
}
