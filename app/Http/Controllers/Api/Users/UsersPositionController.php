<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\PositionRequest;
use App\Models\User;
use App\Policies\PositionPolicy;
use Orion\Http\Controllers\RelationController;

class UsersPositionController extends RelationController
{
    protected $model = User::class;

    protected $request = PositionRequest::class;

    protected $policy = PositionPolicy::class;

    protected $relation = 'position';
}
