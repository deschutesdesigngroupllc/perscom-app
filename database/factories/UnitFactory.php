<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => "Unit {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
            'hidden' => false,
        ];
    }
}
