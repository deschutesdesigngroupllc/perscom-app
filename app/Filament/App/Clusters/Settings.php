<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters;

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
        return Auth::user()->hasRole(Utils::getSuperAdminName());
    }
}
