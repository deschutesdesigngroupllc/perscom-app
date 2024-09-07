<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Positions;

use App\Http\Requests\Api\PositionRequest;
use App\Models\Position;
use App\Policies\PositionPolicy;
use Orion\Http\Controllers\Controller;

class PositionsController extends Controller
{
    protected $model = Position::class;

    protected $request = PositionRequest::class;

    protected $policy = PositionPolicy::class;

    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'users'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }
}
