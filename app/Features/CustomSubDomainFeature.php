<?php

declare(strict_types=1);

namespace App\Features;

use Illuminate\Support\Facades\App;
use Spark\Plan;

use function in_array;

class CustomSubDomainFeature extends BaseFeature
{
    public function resolve(?string $scope): bool
    {
        $tenant = static::resolveTenant($scope);

        return match (true) {
            App::isAdmin() && ! App::runningInConsole() => false,
            App::isDemo() => false,
            $tenant?->onTrial() => false,
            optional($tenant->sparkPlan(), static fn (Plan $plan): bool => in_array(self::class, $plan->options, true)) === true => true,
            default => false,
        };
    }
}
