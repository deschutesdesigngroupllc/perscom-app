<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Specialty>
 */
class SpecialtyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'abbreviation' => Str::upper($this->faker->lexify('???')),
            'description' => $this->faker->paragraph,
        ];
    }
}
