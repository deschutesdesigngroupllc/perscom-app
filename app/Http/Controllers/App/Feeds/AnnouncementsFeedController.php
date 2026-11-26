<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Feeds;

use App\Models\Announcement;
use App\Models\User;
use App\Services\CalendarFeedService;
use App\Services\RssFeedService;
use Illuminate\Http\Response;

class AnnouncementsFeedController
{
    public function __invoke(string $token): Response
    {
        $user = CalendarFeedService::findUser($token);

        abort_unless($user instanceof User, 404);
        abort_unless($user->can('viewAny', Announcement::class), 403);

        return RssFeedService::toResponse(RssFeedService::announcements());
    }
}
