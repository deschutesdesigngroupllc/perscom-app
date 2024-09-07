<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Models\User;
use App\Policies\FieldPolicy;
use Orion\Http\Controllers\RelationController;

class UsersFieldsController extends RelationController
{
    protected $model = User::class;

    protected $policy = FieldPolicy::class;

    protected $relation = 'fields';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['order'];
}
