<?php

declare(strict_types=1);

namespace App\Features;

use App\Models\Tenant;

use function tenant;

class BaseFeature
{
    protected static ?Tenant $tenant = null;

    public static function resetTenant(): void
    {
        self::$tenant = null;
    }

    public static function resolveTenant(?string $scope = null): ?Tenant
    {
        return self::$tenant ??= match (true) {
            filled($scope) => Tenant::find($scope),
            tenancy()->initialized => tenant(),
            default => null
        };
    }
}
