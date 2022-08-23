<?php

namespace Database\Factories\Forms;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
		$form = "Form  {$this->faker->unique()->randomNumber()}";
        return [
	        'name' => $form,
	        'slug' => Str::slug($form),
	        'success_message' => $this->faker->sentence,
	        'is_public' => $this->faker->boolean,
	        'description' => $this->faker->paragraph,
	        'instructions' => $this->faker->paragraph
        ];
    }
}
