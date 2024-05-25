<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Http\Requests\Api\GroupRequest;
use App\Models\Group;
use App\Policies\GroupPolicy;
use Orion\Http\Controllers\Controller;

class GroupsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Group::class;

    /**
     * @var string
     */
    protected $request = GroupRequest::class;

    /**
     * @var string
     */
    protected $policy = GroupPolicy::class;

    /**
     * @return string[]
     */
    public function exposedScopes(): array
    {
        return ['orderForRoster'];
    }

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['units', 'units.*', 'units.users', 'units.users.*'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }
}
