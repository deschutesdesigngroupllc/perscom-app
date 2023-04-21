<?php

namespace App\Features;

use App\Models\Tenant;
use Illuminate\Support\Optional;

class BaseFeature
{
    /**
     * @var Tenant|null
     */
    protected Tenant|null $tenant = null;

    /**
     * @return Tenant|Optional
     */
    public function resolveTenant(string|null $scope)
    {
        return $this->tenant = optional($scope, static function ($scope) {
            return Tenant::findOrFail($scope);
        });
    }
}
