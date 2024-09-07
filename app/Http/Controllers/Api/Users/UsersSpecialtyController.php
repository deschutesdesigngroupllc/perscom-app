<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\SpecialtyRequest;
use App\Models\User;
use App\Policies\SpecialtyPolicy;
use Orion\Http\Controllers\RelationController;

class UsersSpecialtyController extends RelationController
{
    protected $model = User::class;

    protected $request = SpecialtyRequest::class;

    protected $policy = SpecialtyPolicy::class;

    protected $relation = 'specialty';
}
