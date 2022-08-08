<?php

namespace App\Nova\Dashboards;

use App\Models\Announcement;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\UpdatesPerformed;
use App\Nova\Metrics\UsersOnline;
use Laravel\Nova\Dashboards\Main as Dashboard;
use Perscom\AlertCard\AlertCard;
use Perscom\DashboardQuickActions\DashboardQuickActions;

class Main extends Dashboard
{
    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public function name()
    {
        return 'Dashboard';
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        $announcements = Announcement::query()
            ->whereDate('expires_at', '>', now())
            ->orWhereNull('expires_at')
            ->get()
            ->map(function ($announcement) {
                return [
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'color' => $announcement->color,
                ];
            })
            ->toArray();
        return [
            (new AlertCard())->withAnnouncements($announcements),
            new NewUsers(),
            new UsersOnline(),
            new UpdatesPerformed(),
            new DashboardQuickActions(),
        ];
    }
}
