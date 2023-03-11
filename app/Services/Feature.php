<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Request;
use Spark\Plan;

class Feature
{
    /**
     * @param  \Laravel\Pennant\Feature  $feature
     * @param  bool  $enabledInDemoMode
     * @param  bool  $enabledInTrial
     * @param  bool  $enabledWhenBillingIsOff
     * @param  bool  $isAccessibleForAdmin
     * @return bool|\Illuminate\Support\Optional|mixed|\Stancl\Tenancy\Contracts\Tenant
     */
    public function isAccessible(
        $feature,
        bool $enabledInDemoMode = true,
        bool $enabledInTrial = true,
        bool $enabledWhenBillingIsOff = true,
        bool $isAccessibleForAdmin = false)
    {
        return optional(tenant(), static function (Tenant $tenant) use ($feature, $enabledInTrial) {
            return optional($tenant->sparkPlan(), static function (Plan $plan) use ($feature) {
                return \in_array($feature, $plan->options, true);
            }) ?: ($enabledInTrial && $tenant->onTrial());
        })
            ?: (Request::isDemoMode() && $enabledInDemoMode)
                ?: (FeatureFlag::isOff('billing') && $enabledWhenBillingIsOff)
                    ?: (Request::isCentralRequest() && $isAccessibleForAdmin)
                        ?: false;
    }
}
