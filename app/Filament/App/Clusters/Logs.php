<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;

class Logs extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 8;

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }
}
