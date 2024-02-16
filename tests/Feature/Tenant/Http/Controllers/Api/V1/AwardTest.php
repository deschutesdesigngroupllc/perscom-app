<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Awards\AwardsController;
use App\Models\Award;
use Illuminate\Database\Eloquent\Factories\Factory;

class AwardTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'awards';
    }

    public function controller(): string
    {
        return AwardsController::class;
    }

    public function model(): string
    {
        return Award::class;
    }

    public function factory(): Factory
    {
        return Award::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:award',
            'show' => 'view:award',
            'store' => 'create:award',
            'update' => 'update:award',
            'delete' => 'delete:award',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => 'Test Award',
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
