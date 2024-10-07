<?php

declare(strict_types=1);

namespace App\Features;

use Illuminate\Support\Facades\App;
use Spark\Plan;

class AdvancedNotificationsFeature extends BaseFeature
{
    public function resolve(?string $scope): bool
    {
        $tenant = $this->resolveTenant($scope);
        $premiumFeatures = config('spark.premium_features');

        return match (true) {
            App::isAdmin() => false,
            App::isDemo() => true,
            $tenant?->onTrial() => true,
            $tenant->subscribedToPrice(data_get($premiumFeatures, static::class)) => true,
            optional($tenant->sparkPlan(), static function (Plan $plan) {
                return in_array(__CLASS__, $plan->options, true);
            }) === true => true,
            default => false,
        };
    }
}
