<?php

namespace App\Http\Controllers\Api\V1\Units;

use App\Models\Unit;
use Orion\Http\Controllers\RelationController;

class UnitsUsersController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Unit::class;

    /**
     * @var string
     */
    protected $relation = 'users';
}
