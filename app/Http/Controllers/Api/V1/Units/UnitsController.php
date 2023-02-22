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
    public function includes(): array
    {
        return ['position', 'rank', 'specialty', 'status', 'unit'];
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
        return ['id', 'name'];
    }
}
