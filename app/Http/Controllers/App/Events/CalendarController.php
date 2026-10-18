<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Events;

use App\Data\Events\OccurrenceData;
use App\Models\Event;
use App\Services\CalendarFeedService;
use App\Services\EventOccurrenceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class CalendarController
{
    /**
     * Download an event as an .ics file. With an occurrence timestamp only that date is exported,
     * otherwise the whole event (including its repeat rule) is.
     */
    public function __invoke(Request $request, Event $event): Response
    {
        abort_unless(Auth::user()?->can('view', $event), 403);

        $filename = sprintf('%s.ics', Str::slug($event->name) ?: 'event');
        $timestamp = $request->query('occurrence');

        if (is_string($timestamp) && ctype_digit($timestamp)) {
            $occurrence = EventOccurrenceService::find($event, Date::createFromTimestamp((int) $timestamp, config('app.timezone')));

            abort_unless($occurrence instanceof OccurrenceData, 404);

            return CalendarFeedService::toResponse(CalendarFeedService::renderOccurrence($occurrence), $filename, download: true);
        }

        return CalendarFeedService::toResponse(CalendarFeedService::render([$event], $event->name), $filename, download: true);
    }
}
