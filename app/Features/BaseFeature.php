<?php

namespace App\Features;

use App\Models\Tenant;
use Illuminate\Support\Optional;

class BaseFeature
{
    protected ?Tenant $tenant = null;

    public function resolveTenant(?string $scope): Tenant|Optional|null
    {
        if (is_null($this->tenant)) {
            return $this->tenant = optional($scope, static function ($scope) {
                return Tenant::findOrFail($scope);
            });
        }

        return $this->tenant;
    }
}
