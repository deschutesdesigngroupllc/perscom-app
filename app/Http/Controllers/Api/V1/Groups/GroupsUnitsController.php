<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Http\Requests\Api\UnitRequest;
use App\Models\Group;
use App\Policies\UnitPolicy;
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
    protected $request = UnitRequest::class;

    /**
     * @var string
     */
    protected $policy = UnitPolicy::class;

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
