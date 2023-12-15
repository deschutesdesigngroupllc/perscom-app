<?php

namespace Database\Factories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
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
