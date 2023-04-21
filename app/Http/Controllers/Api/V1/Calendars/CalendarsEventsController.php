<?php

namespace App\Http\Controllers\Api\V1\Calendars;

use App\Models\Calendar;
use Orion\Http\Controllers\RelationController;

class CalendarsEventsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Calendar::class;

    /**
     * @var string
     */
    protected $relation = 'events';
}
