<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Models\Group;
use Orion\Http\Controllers\RelationController;

class GroupsUnitsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Group::class;

    /**
     * @var string
     */
    protected $relation = 'units';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['users', 'users.*'];
    }
}
