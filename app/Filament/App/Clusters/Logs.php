<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Panel;
use UnitEnum;

class Logs extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-command-line';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 8;

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }
}
