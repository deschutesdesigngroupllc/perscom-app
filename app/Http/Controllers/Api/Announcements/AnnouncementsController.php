<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Announcements;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\AnnouncementRequest;
use App\Models\Announcement;
use Orion\Http\Controllers\Controller;

class AnnouncementsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Announcement::class;

    protected $request = AnnouncementRequest::class;

    public function exposedScopes(): array
    {
        return ['enabled', 'disabled'];
    }

    public function sortableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'global', 'enabled', 'expires_at', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'global', 'enabled', 'expires_at', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'global', 'enabled', 'expires_at', 'created_at', 'updated_at'];
    }
}
