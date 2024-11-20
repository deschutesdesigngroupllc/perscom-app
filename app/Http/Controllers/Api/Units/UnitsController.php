<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Units;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\UnitRequest;
use App\Models\Unit;
use Orion\Http\Controllers\Controller;

class UnitsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Unit::class;

    protected $request = UnitRequest::class;

    public function exposedScopes(): array
    {
        return ['hidden', 'visible'];
    }

    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'image', 'groups', 'groups.*', 'users', 'users.*'];
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
