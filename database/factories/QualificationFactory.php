<?php

namespace Database\Factories;

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
            'name' => "Qualification {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
        ];
    }
}
