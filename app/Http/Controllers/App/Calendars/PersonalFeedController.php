<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Calendars;

use App\Models\Event;
use App\Models\User;
use App\Services\CalendarFeedService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PersonalFeedController
{
    public function __invoke(Request $request, string $token): Response
    {
        $user = CalendarFeedService::findUser($token);

        abort_unless($user instanceof User, 404);
        abort_unless($user->can('viewAny', Event::class), 403);

        $calendarIds = Str::of((string) $request->query('calendars', ''))
            ->explode(',')
            ->map(fn (string $id): string => trim($id))
            ->filter(fn (string $id): bool => ctype_digit($id))
            ->map(fn (string $id): int => (int) $id)
            ->values()
            ->all();

        return CalendarFeedService::toResponse(
            CalendarFeedService::render(CalendarFeedService::feedEvents($user, $calendarIds), CalendarFeedService::personalFeedName()),
            'calendar.ics',
        );
    }
}
