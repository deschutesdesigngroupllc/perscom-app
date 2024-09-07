<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\UnitRequest;
use App\Models\User;
use App\Policies\UnitPolicy;
use Orion\Http\Controllers\RelationController;

class UsersUnitController extends RelationController
{
    protected $model = User::class;

    protected $request = UnitRequest::class;

    protected $policy = UnitPolicy::class;

    protected $relation = 'unit';
}
