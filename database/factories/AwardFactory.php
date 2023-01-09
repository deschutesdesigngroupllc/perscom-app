<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AwardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => "Award {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
        ];
    }
}
