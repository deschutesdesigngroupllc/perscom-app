<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\AccountWidget as BaseAccountWidget;

class AccountWidget extends BaseAccountWidget
{
    use HasWidgetShield;

    protected static ?int $sort = -4;
}
