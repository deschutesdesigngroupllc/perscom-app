<?php

declare(strict_types=1);

namespace App\Actions\Features;

use App\Models\Feature;
use App\Models\Tenant;
use Laravel\Cashier\Exceptions\SubscriptionUpdateFailure;
use Laravel\Cashier\Subscription;

class StopFeature
{
    /**
     * @throws SubscriptionUpdateFailure
     */
    public static function handle(Tenant $tenant, Feature $feature): Subscription|bool
    {
        $subscription = $tenant->subscription();
        if (is_null($subscription) || is_null($feature->price_id)) {
            return false;
        }

        return rescue(function () use ($subscription, $feature) {
            return $subscription
                ->removePrice($feature->price_id);
        }, false);
    }
}
