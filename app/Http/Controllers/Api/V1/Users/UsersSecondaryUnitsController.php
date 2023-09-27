<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\UnitRequest;
use App\Models\User;
use App\Policies\UnitPolicy;
use Orion\Http\Controllers\RelationController;

class UsersSecondaryUnitsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = UnitRequest::class;

    /**
     * @var string
     */
    protected $policy = UnitPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'secondary_units';
}
