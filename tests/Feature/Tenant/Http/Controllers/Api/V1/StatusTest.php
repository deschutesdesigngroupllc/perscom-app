<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'statuses';
    }

    public function model(): string
    {
        return Status::class;
    }

    public function factory(): Factory
    {
        return Status::factory();
    }

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

    public function storeData(): array
    {
        return [
            'name' => 'Test Status',
            'text_color' => $this->faker->hexColor,
            'bg_color' => $this->faker->hexColor,
        ];
    }

    public function updateData(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
