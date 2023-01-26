<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Http\Resources\Api\Widget\RosterCollectionResource;
use App\Http\Resources\Api\Widget\RosterResource;
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
}
