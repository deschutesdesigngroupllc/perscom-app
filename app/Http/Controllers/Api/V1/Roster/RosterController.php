<?php

namespace App\Http\Controllers\Api\V1\Roster;

use App\Http\Resources\Api\RosterResource;
use App\Models\Unit;
use Orion\Http\Controllers\Controller;

class RosterController extends Controller
{
    /**
     * @var string
     */
    protected $model = Unit::class;

    /**
     * @var string
     */
    protected $resource = RosterResource::class;

    /**
     * @return int
     */
    public function limit(): int
    {
        return 0;
    }
}
