<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => "Task {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
            'instructions' => $this->faker->paragraph,
        ];
    }
}
