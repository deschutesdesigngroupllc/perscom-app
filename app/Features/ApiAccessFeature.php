<?php

declare(strict_types=1);

namespace App\Features;

use Exception;
use Illuminate\Support\Facades\App;
use Spark\Plan;

use function in_array;

class ApiAccessFeature extends BaseFeature
{
    /**
     * @throws Exception
     */
    public function resolve(?string $scope): bool
    {
        $tenant = static::resolveTenant($scope);

        return match (true) {
            App::isAdmin() && ! App::runningInConsole() => false,
            App::isDemo() => true,
            $tenant?->onTrial() => true,
            optional($tenant->sparkPlan(), static function (Plan $plan) {
                return in_array(__CLASS__, $plan->options, true);
            }) === true => true,
            default => false,
        };
    }
}
