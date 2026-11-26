<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Feeds;

use App\Models\Newsfeed;
use App\Models\User;
use App\Services\CalendarFeedService;
use App\Services\RssFeedService;
use Illuminate\Http\Response;

class NewsFeedController
{
    public function __invoke(string $token): Response
    {
        $user = CalendarFeedService::findUser($token);

        abort_unless($user instanceof User, 404);
        abort_unless($user->can('view', Newsfeed::class), 403);

        return RssFeedService::toResponse(RssFeedService::news());
    }
}
