<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters;

use App;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Clusters\Cluster;
use Filament\Panel;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Settings extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 8;

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Auth::user()->hasRole(Utils::getSuperAdminName())
            && ! App::isDemo();
    }
}
