<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Units;

use App\Http\Requests\Api\UnitRequest;
use App\Models\Unit;
use App\Policies\UnitPolicy;
use Orion\Http\Controllers\Controller;

class UnitsController extends Controller
{
    protected $model = Unit::class;

    protected $request = UnitRequest::class;

    protected $policy = UnitPolicy::class;

    public function exposedScopes(): array
    {
        return ['hidden', 'visible'];
    }

    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'groups', 'users'];
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
