<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\PositionRequest;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersPositionController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = PositionRequest::class;

    protected $relation = 'position';
}
