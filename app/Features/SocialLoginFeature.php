<?php

declare(strict_types=1);

namespace App\Features;

use Illuminate\Support\Facades\App;
use Spark\Plan;

use function in_array;

class SocialLoginFeature extends BaseFeature
{
    public function resolve(?string $scope): bool
    {
        $tenant = $this->resolveTenant($scope);

        return match (true) {
            App::isAdmin() => false,
            App::isDemo() => false,
            $tenant?->onTrial() => true,
            optional($tenant->sparkPlan(), static function (Plan $plan) {
                return in_array(__CLASS__, $plan->options, true);
            }) === true => true,
            default => false,
        };
    }
}
