<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Groups;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\UnitRequest;
use App\Models\Group;
use Orion\Http\Controllers\RelationController;

class GroupsUnitsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Group::class;

    protected $request = UnitRequest::class;

    protected $relation = 'units';

    protected $pivotFillable = ['order'];

    /**
     * @return array<int, string>
     */
    public function exposedScopes(): array
    {
        return ['hidden', 'visible'];
    }

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'image', 'groups', 'groups.*', 'users', 'users.*'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'empty', 'order', 'hidden', 'icon', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'empty', 'order', 'hidden', 'icon', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'empty', 'order', 'hidden', 'icon', 'created_at', 'updated_at'];
    }
}
