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
	        'description' => $this->faker->paragraph,
	        'instructions' => $this->faker->randomHtml
        ];
    }
}
