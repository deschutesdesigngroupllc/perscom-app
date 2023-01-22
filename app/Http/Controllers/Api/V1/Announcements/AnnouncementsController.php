<?php

namespace App\Http\Controllers\Api\V1\Announcements;

use App\Http\Requests\AnnouncementRequest;
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
    public function searchableBy(): array
    {
        return ['title'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'title', 'created_at', 'expires_at'];
    }
}
