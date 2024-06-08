<?php

namespace App\Http\Controllers\Api\V1\Units;

use App\Http\Requests\Api\UnitRequest;
use App\Models\Unit;
use App\Policies\UnitPolicy;
use Orion\Http\Controllers\Controller;

class UnitsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Unit::class;

    /**
     * @var string
     */
    protected $request = UnitRequest::class;

    /**
     * @var string
     */
    protected $policy = UnitPolicy::class;

    /**
     * @return string[]
     */
    public function exposedScopes(): array
    {
        return ['hidden', 'visible'];
    }

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'groups', 'users'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'hidden', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'hidden', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'hidden', 'created_at', 'updated_at', 'deleted_at'];
    }
}
