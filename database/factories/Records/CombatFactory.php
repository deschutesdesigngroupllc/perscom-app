<?php

namespace Database\Factories\Records;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CombatFactory extends Factory
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
        ];
    }
}
