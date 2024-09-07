<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => "Task {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
            'instructions' => $this->faker->paragraph,
        ];
    }
}
