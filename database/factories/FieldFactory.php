<?php

namespace Database\Factories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    	$field = "Field {$this->faker->unique()->randomNumber()}";
    	return [
            'name' => $field,
		    'key' => Str::slug($field),
		    'description' => $this->faker->paragraph,
		    'required' => $this->faker->boolean,
		    'help' => $this->faker->sentence,
		    'placeholder' => $this->faker->sentence(3),
		    'type' => $this->faker->randomElement(collect(Field::$fieldTypes)->keys())
        ];
    }
}
