<?php

declare(strict_types=1);

namespace App\Features;

use App\Models\Tenant;

class BaseFeature
{
    protected static ?Tenant $tenant = null;

    public static function resetTenant(): void
    {
        self::$tenant = null;
    }

    public function resolveTenant(?string $scope): ?Tenant
    {
        return self::$tenant ??= ! is_null($scope)
            ? Tenant::find($scope)
            : null;
    }
}
