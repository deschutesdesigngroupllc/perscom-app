<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\User;

class UserTest extends ApiResourceTestCase
{
    /**
     * @return string
     */
    public function endpoint()
    {
        return 'users';
    }

    /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @return \Database\Factories\UserFactory
     */
    public function factory()
    {
        return User::factory();
    }

    /**
     * @return string[]
     */
    public function scopes()
    {
        return [
            'index' => 'view:user',
            'show' => 'view:user',
            'store' => 'create:user',
            'update' => 'update:user',
            'delete' => 'delete:user'
        ];
    }

    /**
     * @return array
     */
    public function storeData()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email
        ];
    }

    /**
     * @return array
     */
    public function updateData()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
