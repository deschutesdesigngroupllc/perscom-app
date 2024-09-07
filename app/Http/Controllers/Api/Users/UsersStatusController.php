<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Models\User;
use App\Policies\StatusPolicy;
use Orion\Http\Controllers\RelationController;

class UsersStatusController extends RelationController
{
    protected $model = User::class;

    protected $policy = StatusPolicy::class;

    protected $relation = 'status';
}
