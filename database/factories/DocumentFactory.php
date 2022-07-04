<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
	        'name' => "Document {$this->faker->unique()->randomNumber()}",
	        'description' => $this->faker->paragraph,
	        'content' => $this->faker->randomHtml
        ];
    }
}
