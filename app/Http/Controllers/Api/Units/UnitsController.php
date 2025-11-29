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
