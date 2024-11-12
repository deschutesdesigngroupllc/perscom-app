<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Events;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\EventRequest;
use App\Models\Event;
use Orion\Http\Controllers\Controller;

class EventsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Event::class;

    protected $request = EventRequest::class;

    public function exposedScopes(): array
    {
        return ['datePeriod', 'tags'];
    }

    public function includes(): array
    {
        return [
            'attachments',
            'author',
            'author.*',
            'calendar',
            'calendar.*',
            'comments',
            'comments.*',
            'image',
            'schedule',
            'tags',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'calendar.*', 'description', 'content', 'location', 'url', 'author_id', 'author.*', 'all_day', 'start', 'end', 'repeats', 'frequency', 'interval', 'end_type', 'count', 'until', 'by_day', 'by_month', 'by_set_position', 'by_month_day', 'by_year_day', 'rrule', 'registration_enabled', 'registration_deadline', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'description', 'content', 'location', 'url', 'author_id', 'all_day', 'start', 'end', 'repeats', 'frequency', 'interval', 'end_type', 'count', 'until', 'by_day', 'by_month', 'by_set_position', 'by_month_day', 'by_year_day', 'rrule', 'registration_enabled', 'registration_deadline', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'calendar.*', 'description', 'content', 'location', 'url', 'author_id', 'author.*', 'all_day', 'start', 'end', 'repeats', 'frequency', 'interval', 'end_type', 'count', 'until', 'by_day', 'by_month', 'by_set_position', 'by_month_day', 'by_year_day', 'rrule', 'registration_enabled', 'registration_deadline', 'created_at', 'updated_at', 'deleted_at'];
    }
}
