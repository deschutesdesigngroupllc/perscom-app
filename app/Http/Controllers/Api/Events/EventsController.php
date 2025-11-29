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

    /**
     * @return array<int, string>
     */
    public function exposedScopes(): array
    {
        return ['datePeriod', 'tags'];
    }

    /**
     * @return array<int, string>
     */
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

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'calendar.*', 'description', 'content', 'location', 'url', 'author_id', 'author.*', 'all_day', 'starts', 'ends', 'repeats', 'registration_enabled', 'registration_deadline', 'notifications_enabled', 'notifications_interval', 'notifications_channels', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'calendar.*', 'description', 'content', 'location', 'url', 'author_id', 'author.*', 'all_day', 'starts', 'ends', 'repeats', 'registration_enabled', 'registration_deadline', 'notifications_enabled', 'notifications_interval', 'notifications_channels', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'calendar_id', 'calendar.*', 'description', 'content', 'location', 'url', 'author_id', 'author.*', 'all_day', 'starts', 'ends', 'repeats', 'registration_enabled', 'registration_deadline', 'notifications_enabled', 'notifications_interval', 'notifications_channels', 'created_at', 'updated_at'];
    }
}
