<?php

namespace Database\Factories\Records;

use App\Models\Qualification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualificationFactory extends Factory
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
            'qualification_id' => Qualification::factory(),
        ];
    }
}
