<?php

namespace App\Http\Controllers\Api\V1\Calendars;

use App\Http\Requests\Api\CalendarRequest;
use App\Models\Calendar;
use App\Policies\CalendarPolicy;
use Orion\Http\Controllers\Controller;

class CalendarsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Calendar::class;

    /**
     * @var string
     */
    protected $request = CalendarRequest::class;

    /**
     * @var string
     */
    protected $policy = CalendarPolicy::class;

    /**
     * @return string[]
     */
    public function exposedScopes(): array
    {
        return ['tags'];
    }

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['events', 'events.author', 'tags'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'color', 'created_at', 'updated_at', 'deleted_at'];
    }
}
