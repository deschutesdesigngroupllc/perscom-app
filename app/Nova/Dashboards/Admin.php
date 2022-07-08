<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\Admin\AverageRevenue;
use App\Nova\Metrics\Admin\NewTenants;
use App\Nova\Metrics\Admin\SubscriptionByStatus;
use App\Nova\Metrics\Admin\SubscriptionsByPlan;
use App\Nova\Metrics\Admin\TotalRevenue;
use App\Nova\Metrics\UsersOnline;
use Laravel\Nova\Dashboard;

class Admin extends Dashboard
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
     * Get the URI key of the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'main';
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new NewTenants(),
            new TotalRevenue(),
            new AverageRevenue(),
            new UsersOnline(),
            new SubscriptionsByPlan(),
            new SubscriptionByStatus(),
        ];
    }
}
