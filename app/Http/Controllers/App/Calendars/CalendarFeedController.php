<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Calendars;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use App\Services\CalendarFeedService;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

use function sprintf;

class CalendarFeedController
{
    public function __invoke(string $token, Calendar $calendar): Response
    {
        $user = CalendarFeedService::findUser($token);

        abort_unless($user instanceof User, 404);
        abort_unless($user->can('view', Event::class), 403);

        return CalendarFeedService::toResponse(
            CalendarFeedService::render(CalendarFeedService::feedEvents($user, [$calendar->getKey()]), CalendarFeedService::calendarFeedName($calendar)),
            sprintf('%s.ics', Str::slug($calendar->name) ?: 'calendar'),
        );
    }
}
