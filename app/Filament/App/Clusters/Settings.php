<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters;

use App;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Auth;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 8;

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Auth::user()->hasRole(Utils::getSuperAdminName())
            && ! App::isDemo();
    }
}
