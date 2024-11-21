<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Users\UsersController;
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

    /**
     * @return Factory<User>
     */
    public function factory(): Factory
    {
        return User::factory();
    }

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test User',
            'email' => $this->faker->email,
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
