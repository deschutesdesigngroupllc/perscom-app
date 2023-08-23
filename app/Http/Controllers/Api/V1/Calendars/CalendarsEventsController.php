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

    /**
     * The list of available query scopes.
     */
    public function exposedScopes(): array
    {
        return ['datePeriod', 'tags'];
    }

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['author', 'calendar', 'tags'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['name', 'calendar_id', 'start', 'end'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'start', 'end', 'created_at'];
    }
}
