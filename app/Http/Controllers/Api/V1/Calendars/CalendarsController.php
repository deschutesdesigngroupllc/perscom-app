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
    public function includes(): array
    {
        return ['events', 'author'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['name', 'timezone', 'author_id'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'timezone', 'author_id', 'created_at'];
    }
}
