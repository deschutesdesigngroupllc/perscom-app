<?php

namespace Database\Factories;

use App\Models\Award;
use App\Models\Category;
use App\Models\Qualification;
use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
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
