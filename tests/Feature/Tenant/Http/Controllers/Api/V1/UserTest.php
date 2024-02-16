<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Users\UsersController;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'users';
    }

    public function controller(): string
    {
        return UsersController::class;
    }

    public function model(): string
    {
        return User::class;
    }

    public function factory(): Factory
    {
        return User::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:user',
            'show' => 'view:user',
            'store' => 'create:user',
            'update' => 'update:user',
            'delete' => 'delete:user',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => 'Test User',
            'email' => $this->faker->email,
        ];
    }

    public function updateData(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
