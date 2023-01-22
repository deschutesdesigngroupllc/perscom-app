<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersCombatRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $relation = 'combat_records';
}
