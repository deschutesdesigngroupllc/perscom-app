<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Tasks\TasksController;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'tasks';
    }

    public function controller(): string
    {
        return TasksController::class;
    }

    public function model(): string
    {
        return Task::class;
    }

    /**
     * @return Factory<Task>
     */
    public function factory(): Factory
    {
        return Task::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:task',
            'show' => 'view:task',
            'store' => 'create:task',
            'update' => 'update:task',
            'delete' => 'delete:task',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'title' => 'Test Task',
            'description' => $this->faker->sentence,
            'instructions' => $this->faker->sentence,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'title' => $this->faker->name,
        ];
    }
}
