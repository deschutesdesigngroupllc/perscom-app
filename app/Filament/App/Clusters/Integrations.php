<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use UnitEnum;

/**
 * Everything that connects PERSCOM to other services: chat and SSO providers, AI tools,
 * and the developer surface (API keys, OAuth apps, webhooks and embeddable widgets).
 */
class Integrations extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string|UnitEnum|null $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 2;
}
