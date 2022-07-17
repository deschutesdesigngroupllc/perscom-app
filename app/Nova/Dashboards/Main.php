<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\UpdatesPerformed;
use App\Nova\Metrics\UsersOnline;
use Laravel\Nova\Dashboards\Main as Dashboard;
use Perscom\DashboardQuickActions\DashboardQuickActions;
use Perscom\LatestFormSubmissions\LatestFormSubmissions;

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
        return [new NewUsers(), new UpdatesPerformed(), new UsersOnline(), new DashboardQuickActions()];
    }
}
