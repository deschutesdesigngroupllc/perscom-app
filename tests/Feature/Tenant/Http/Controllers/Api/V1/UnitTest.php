<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'units';
    }

    public function model(): string
    {
        return Unit::class;
    }

    public function factory(): Factory
    {
        return Unit::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:unit',
            'show' => 'view:unit',
            'store' => 'create:unit',
            'update' => 'update:unit',
            'delete' => 'delete:unit',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => 'Test Unit',
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
