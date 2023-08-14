<?php

namespace App\Features;

use Illuminate\Support\Facades\Request;
use Spark\Plan;

class SocialLoginFeature extends BaseFeature
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(?string $scope): mixed
    {
        $tenant = $this->resolveTenant($scope);

        return match (true) {
            Request::isCentralRequest() => false,
            Request::isDemoMode() => false,
            \request()->routeIs('auth.*') => true,
            $tenant->onTrial() => true,
            optional($tenant->sparkPlan(), static function (Plan $plan) {
                return \in_array(__CLASS__, $plan->options, true);
            }) => true,
            default => false,
        };
    }
}
