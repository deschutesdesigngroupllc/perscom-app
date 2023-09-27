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
