<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Models\User;
use App\Policies\FieldPolicy;
use Orion\Http\Controllers\RelationController;

class UsersFieldsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $policy = FieldPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'fields';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['order'];
}
