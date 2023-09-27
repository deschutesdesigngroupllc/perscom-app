<?php

namespace App\Http\Controllers\Api\V1\Positions;

use App\Http\Requests\Api\PositionRequest;
use App\Models\Position;
use App\Policies\PositionPolicy;
use Orion\Http\Controllers\Controller;

class PositionsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Position::class;

    /**
     * @var string
     */
    protected $request = PositionRequest::class;

    /**
     * @var string
     */
    protected $policy = PositionPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'users'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['name'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'created_at'];
    }
}
