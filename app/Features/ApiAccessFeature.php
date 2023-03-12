<?php

namespace App\Features;

use App\Models\Tenant;
use Illuminate\Support\Facades\Request;
use Spark\Plan;

class ApiAccessFeature
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(Tenant|null $scope): mixed
    {
        return match (true) {
            Request::isCentralRequest() => false,
            Request::isDemoMode() => true,
            $scope?->onTrial() => true,
            optional($scope?->sparkPlan(), static function (Plan $plan) {
                return \in_array(__CLASS__, $plan->options, true);
            }) => true,
            default => false,
        };
    }
}
