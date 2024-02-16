<?php

namespace App\Http\Controllers\Api\V1\Events;

use App\Http\Requests\Api\EventRequest;
use App\Models\Event;
use App\Policies\EventPolicy;
use Orion\Http\Controllers\Controller;

class EventsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Event::class;

    /**
     * @var string
     */
    protected $request = EventRequest::class;

    /**
     * @var string
     */
    protected $policy = EventPolicy::class;

    /**
     * @return string[]
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
    public function sortableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'calendar.*', 'description', 'content', 'location', 'url', 'author_id', 'author.*', 'all_day', 'start', 'end', 'repeats', 'frequency', 'interval', 'end_type', 'count', 'until', 'by_day', 'by_month', 'by_set_position', 'by_month_day', 'by_year_day', 'rrule', 'registration_enabled', 'registration_deadline', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'description', 'content', 'location', 'url', 'author_id', 'all_day', 'start', 'end', 'repeats', 'frequency', 'interval', 'end_type', 'count', 'until', 'by_day', 'by_month', 'by_set_position', 'by_month_day', 'by_year_day', 'rrule', 'registration_enabled', 'registration_deadline', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'calendar.*', 'description', 'content', 'location', 'url', 'author_id', 'author.*', 'all_day', 'start', 'end', 'repeats', 'frequency', 'interval', 'end_type', 'count', 'until', 'by_day', 'by_month', 'by_set_position', 'by_month_day', 'by_year_day', 'rrule', 'registration_enabled', 'registration_deadline', 'created_at', 'updated_at'];
    }
}
