<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersFieldsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $relation = 'fields';

    protected $pivotFillable = ['order'];
}
