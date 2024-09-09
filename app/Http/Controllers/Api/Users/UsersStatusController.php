<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersStatusController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $relation = 'status';
}
