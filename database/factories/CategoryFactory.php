<?php

namespace Database\Factories;

use App\Models\Award;
use App\Models\Qualification;
use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = "Category  {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $category,
            'description' => $this->faker->sentence,
            'resource' => $this->faker->randomElement([Award::class, Rank::class, Qualification::class]),
        ];
    }
}
