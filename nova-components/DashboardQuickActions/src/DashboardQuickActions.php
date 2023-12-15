<?php

namespace Perscom\DashboardQuickActions;

use Laravel\Nova\Card;

class DashboardQuickActions extends Card
{
    /**
     * @var string
     */
    public $width = '1/3';

    public function component(): string
    {
        return 'dashboard-quick-actions';
    }
}
