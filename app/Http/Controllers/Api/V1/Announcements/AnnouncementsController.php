<?php

namespace App\Http\Controllers\Api\V1\Announcements;

use App\Http\Requests\Api\AnnouncementRequest;
use App\Models\Announcement;
use App\Policies\AnnouncementPolicy;
use Orion\Http\Controllers\Controller;

class AnnouncementsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Announcement::class;

    /**
     * @var string
     */
    protected $request = AnnouncementRequest::class;

    /**
     * @var string
     */
    protected $policy = AnnouncementPolicy::class;

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'expires_at', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'expires_at', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'title', 'content', 'color', 'expires_at', 'created_at', 'updated_at', 'deleted_at'];
    }
}
