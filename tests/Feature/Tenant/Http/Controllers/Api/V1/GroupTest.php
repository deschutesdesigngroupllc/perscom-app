<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Groups\GroupsController;
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

    public function factory(): Factory
    {
        return Group::factory();
    }

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

    public function storeData(): array
    {
        return [
            'name' => 'Test Group',
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
