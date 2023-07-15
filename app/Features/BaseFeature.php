<?php

namespace App\Features;

use App\Models\Tenant;

class BaseFeature
{
    protected ?Tenant $tenant = null;

    public function resolveTenant(?string $scope): ?Tenant
    {
        return $this->tenant = optional($scope, static function ($scope) {
            return Tenant::findOrFail($scope);
        });
    }
}
