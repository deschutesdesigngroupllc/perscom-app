<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialtyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    	$number = $this->faker->unique()->randomNumber();
        return [
	        'name' => "Specialty $number",
	        'abbreviation' => "SPC$number",
	        'description' => $this->faker->paragraph,
        ];
    }
}
