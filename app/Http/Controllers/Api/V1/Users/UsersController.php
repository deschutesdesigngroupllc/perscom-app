<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\UserRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Policies\UserPolicy;
use Orion\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = UserRequest::class;

    /**
     * @var string
     */
    protected $policy = UserPolicy::class;

    /**
     * @return string[]
     */
    public function includes() : array
    {
        return ['position', 'rank', 'specialty', 'status', 'unit'];
    }

    /**
     * @return string[]
     */
    public function searchableBy() : array
    {
        return ['name', 'email'];
    }

    /**
     * Display the logged in user
     *
     * @return UserResource
     */
    public function me()
    {
        return UserResource::make(\request()->user());
    }
}
