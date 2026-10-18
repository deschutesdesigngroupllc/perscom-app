<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Units\UnitsController;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'units';
    }

    public function controller(): string
    {
        return UnitsController::class;
    }

    public function model(): string
    {
        return Unit::class;
    }

    /**
     * @return Factory<Unit>
     */
    public function factory(): Factory
    {
        return Unit::factory();
    }

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Unit',
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

    public function test_can_nest_a_unit_beneath_a_parent(): void
    {
        $parent = Unit::factory()->createQuietly();

        $this->withToken($this->apiKey($this->scopes()['update']))
            ->patchJson(sprintf('/%s/%s', $this->endpoint(), $this->factory->getKey()), ['parent_id' => $parent->getKey()])
            ->assertSuccessful()
            ->assertJsonPath('data.parent_id', $parent->getKey());
    }

    public function test_cannot_nest_a_unit_beneath_its_own_descendant(): void
    {
        $child = Unit::factory()->createQuietly(['parent_id' => $this->factory->getKey()]);

        $this->withToken($this->apiKey($this->scopes()['update']))
            ->patchJson(sprintf('/%s/%s', $this->endpoint(), $this->factory->getKey()), ['parent_id' => $child->getKey()])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('parent_id', 'error.errors');
    }
}
