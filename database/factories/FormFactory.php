<?php

namespace Database\Factories;

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
            'description' => $this->faker->paragraph,
            'instructions' => $this->faker->paragraph,
        ];
    }
}
