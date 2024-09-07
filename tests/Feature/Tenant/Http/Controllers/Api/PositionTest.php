<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Positions\PositionsController;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'positions';
    }

    public function controller(): string
    {
        return PositionsController::class;
    }

    public function model(): string
    {
        return Position::class;
    }

    public function factory(): Factory
    {
        return Position::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:position',
            'show' => 'view:position',
            'store' => 'create:position',
            'update' => 'update:position',
            'delete' => 'delete:position',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => 'Test Position',
            'description' => $this->faker->sentence,
        ];
    }

    public function updateData(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
