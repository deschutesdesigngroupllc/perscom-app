<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\Announcements\SendAnnouncement;
use App\Models\Announcement;
use Throwable;

class AnnouncementObserver
{
    /**
     * @throws Throwable
     */
    public function created(Announcement $announcement): void
    {
        SendAnnouncement::handle($announcement);
    }
}
