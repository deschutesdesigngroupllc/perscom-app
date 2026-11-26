<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Calendars;

use App\Models\Event;
use App\Models\User;
use App\Services\CalendarFeedService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PersonalFeedController
{
    public function __invoke(Request $request, string $token): Response
    {
        $user = CalendarFeedService::findUser($token);

        abort_unless($user instanceof User, 404);
        abort_unless($user->can('view', Event::class), 403);

        $calendarIds = collect(Arr::wrap($request->query('calendars')))
            ->filter(fn (mixed $ids): bool => is_string($ids))
            ->flatMap(fn (string $ids): array => Str::of($ids)->explode(',')->all())
            ->map(fn (string $id): string => Str::trim($id))
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
