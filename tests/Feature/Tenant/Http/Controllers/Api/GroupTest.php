<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Groups\GroupsController;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'groups';
    }

    public function controller(): string
    {
        return GroupsController::class;
    }

    public function model(): string
    {
        return Group::class;
    }

    /**
     * @return Factory<Group>
     */
    public function factory(): Factory
    {
        return Group::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:group',
            'show' => 'view:group',
            'store' => 'create:group',
            'update' => 'update:group',
            'delete' => 'delete:group',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Group',
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
