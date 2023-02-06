<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Http\Resources\Api\Widget\RosterResource;
use App\Models\Unit;

class RosterController extends WidgetController
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
