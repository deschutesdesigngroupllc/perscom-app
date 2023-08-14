<?php

namespace App\Features;

use App\Models\Tenant;
use Illuminate\Support\Optional;

class BaseFeature
{
    protected ?Tenant $tenant = null;

    /**
     * @return Tenant|Optional
     */
    public function resolveTenant(?string $scope)
    {
        return $this->tenant = optional($scope, static function ($scope) {
            return Tenant::findOrFail($scope);
        });
    }
}
