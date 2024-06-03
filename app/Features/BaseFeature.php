<?php

namespace App\Features;

use App\Models\Tenant;
use Illuminate\Support\Optional;

class BaseFeature
{
    protected static ?Tenant $tenant = null;

    public function resolveTenant(?string $scope): Tenant|Optional|null
    {
        if (is_null(self::$tenant)) {
            return self::$tenant = optional($scope, static function ($scope) {
                return Tenant::findOrFail($scope);
            });
        }

        return self::$tenant;
    }

    public static function resetTenant(): void
    {
        self::$tenant = null;
    }
}
