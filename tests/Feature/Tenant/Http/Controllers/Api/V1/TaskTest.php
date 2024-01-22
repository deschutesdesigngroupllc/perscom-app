<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'tasks';
    }

    public function model(): string
    {
        return Task::class;
    }

    public function factory(): Factory
    {
        return Task::factory();
    }

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

    public function storeData(): array
    {
        return [
            'title' => 'Test Task',
            'description' => $this->faker->sentence,
            'instructions' => $this->faker->sentence,
        ];
    }

    public function updateData(): array
    {
        return [
            'title' => $this->faker->name,
        ];
    }
}
