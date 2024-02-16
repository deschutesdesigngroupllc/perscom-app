<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Ranks\RanksController;
use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'ranks';
    }

    public function controller(): string
    {
        return RanksController::class;
    }

    public function model(): string
    {
        return Rank::class;
    }

    public function factory(): Factory
    {
        return Rank::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:rank',
            'show' => 'view:rank',
            'store' => 'create:rank',
            'update' => 'update:rank',
            'delete' => 'delete:rank',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => 'Test Rank',
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
