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

    /**
     * @return array<int, string>
     */
    public function exposedScopes(): array
    {
        return ['enabled', 'disabled'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'global', 'enabled', 'expires_at', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'global', 'enabled', 'expires_at', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'global', 'enabled', 'expires_at', 'created_at', 'updated_at'];
    }
}
