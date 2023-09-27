<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\PositionRequest;
use App\Models\User;
use App\Policies\PositionPolicy;
use Orion\Http\Controllers\RelationController;

class UsersPositionController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = PositionRequest::class;

    /**
     * @var string
     */
    protected $policy = PositionPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'position';
}
