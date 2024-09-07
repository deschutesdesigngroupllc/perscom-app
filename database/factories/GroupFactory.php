<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
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
