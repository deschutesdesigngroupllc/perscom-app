<?php

declare(strict_types=1);

namespace App\Features;

use Illuminate\Support\Facades\App;

class AdvancedNotificationsFeature extends BaseFeature
{
    public function resolve(?string $scope): bool
    {
        $tenant = $this->resolveTenant($scope);
        $addons = config('spark.addons');

        return match (true) {
            App::isAdmin() => false,
            App::isDemo() => true,
            $tenant?->onTrial() => true,
            $tenant->subscribedToPrice(data_get($addons, static::class)) => true,
            default => false,
        };
    }
}
