<?php

namespace App\Services;

use App\Models\Enums\FeatureIdentifier;
use App\Models\Tenant;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Illuminate\Support\Facades\Request;
use Spark\Plan;

class Feature
{
    /**
     * @param  FeatureIdentifier  $feature
     * @param  bool  $enabledInDemoMode
     * @param  bool  $enabledInTrial
     * @param  bool  $enabledWhenBillingIsOff
     * @param  bool  $isAccessibleForAdmin
     * @return bool|\Illuminate\Support\Optional|mixed|\Stancl\Tenancy\Contracts\Tenant
     */
    public function isAccessible(
        FeatureIdentifier $feature,
        bool $enabledInDemoMode = true,
        bool $enabledInTrial = true,
        bool $enabledWhenBillingIsOff = true,
        bool $isAccessibleForAdmin = false
    ) {
        return optional(tenant(), static function (Tenant $tenant) use ($feature, $enabledInTrial) {
            return optional($tenant->sparkPlan(), static function (Plan $plan) use ($feature) {
                return \in_array($feature, $plan->options, true);
            }) ?: ($enabledInTrial && $tenant->onTrial());
        })
            ?: (Request::isDemoMode() && $enabledInDemoMode) ?: (FeatureFlag::isOff('billing') &&
                                                                 $enabledWhenBillingIsOff)
                ?: (Request::isCentralRequest() &&
                    $isAccessibleForAdmin) ?: false;
    }
}
