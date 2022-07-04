<?php

namespace Database\Factories;

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
    	$number = $this->faker->unique()->randomNumber();
        return [
	        'name' => "Rank $number",
	        'description' => $this->faker->paragraph,
	        'abbreviation' => "RNK$number",
	        'paygrade' => "R-$number"
        ];
    }
}
