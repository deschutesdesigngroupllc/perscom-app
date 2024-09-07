<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Groups;

use App\Http\Requests\Api\GroupRequest;
use App\Models\Group;
use App\Policies\GroupPolicy;
use Orion\Http\Controllers\Controller;

class GroupsController extends Controller
{
    protected $model = Group::class;

    protected $request = GroupRequest::class;

    protected $policy = GroupPolicy::class;

    public function exposedScopes(): array
    {
        return ['orderForRoster', 'hidden', 'visible'];
    }

    public function includes(): array
    {
        return ['units', 'units.*', 'units.users', 'units.users.*'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'hidden', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'hidden', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'hidden', 'created_at', 'updated_at', 'deleted_at'];
    }
}
