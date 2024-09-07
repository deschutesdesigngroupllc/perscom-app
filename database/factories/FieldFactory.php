<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enums\FieldType;
use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Field>
 */
class FieldFactory extends Factory
{
    public function definition(): array
    {
        $field = "Field {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $field,
            'key' => Str::slug($field, '_'),
            'description' => $this->faker->paragraph,
            'required' => $this->faker->boolean,
            'help' => $this->faker->sentence,
            'placeholder' => $this->faker->sentence(3),
            'type' => $this->faker->randomElement(FieldType::cases()),
        ];
    }
}
