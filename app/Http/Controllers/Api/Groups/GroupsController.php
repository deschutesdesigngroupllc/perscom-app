<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Groups;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\GroupRequest;
use App\Models\Group;
use Orion\Http\Controllers\Controller;

class GroupsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Group::class;

    protected $request = GroupRequest::class;

    /**
     * @return array<int, string>
     */
    public function exposedScopes(): array
    {
        return ['forAutomaticRoster', 'forManualRoster', 'hidden', 'visible'];
    }

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['image', 'units', 'units.*'];
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
