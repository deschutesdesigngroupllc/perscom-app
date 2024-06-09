<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $group = "Group {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $group,
            'description' => $this->faker->sentence,
            'hidden' => false,
        ];
    }
}
