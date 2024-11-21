<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Ranks\RanksController;
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

    /**
     * @return Factory<Rank>
     */
    public function factory(): Factory
    {
        return Rank::factory();
    }

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Rank',
            'description' => $this->faker->sentence,
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
