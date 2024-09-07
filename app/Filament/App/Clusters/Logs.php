<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters;

use Filament\Clusters\Cluster;

class Logs extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 8;
}
