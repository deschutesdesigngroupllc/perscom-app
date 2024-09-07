<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
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
