<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
	    return [
		    'name' => "Status {$this->faker->unique()->randomNumber()}",
		    'color' => $this->faker->hexColor
	    ];
    }
}
