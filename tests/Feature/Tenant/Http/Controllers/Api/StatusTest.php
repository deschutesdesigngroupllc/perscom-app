<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Statuses\StatusesController;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'statuses';
    }

    public function controller(): string
    {
        return StatusesController::class;
    }

    public function model(): string
    {
        return Status::class;
    }

    /**
     * @return Factory<Status>
     */
    public function factory(): Factory
    {
        return Status::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:status',
            'show' => 'view:status',
            'store' => 'create:status',
            'update' => 'update:status',
            'delete' => 'delete:status',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Status',
            'color' => $this->faker->hexColor,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
