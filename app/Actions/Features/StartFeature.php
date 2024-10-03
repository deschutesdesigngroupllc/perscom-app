<?php

declare(strict_types=1);

namespace App\Actions\Features;

use App\Models\Feature;
use App\Models\Tenant;
use Laravel\Cashier\Exceptions\SubscriptionUpdateFailure;
use Laravel\Cashier\Subscription;

class StartFeature
{
    /**
     * @throws SubscriptionUpdateFailure
     */
    public static function handle(Tenant $tenant, Feature $feature): Subscription|false
    {
        return rescue(function () use ($tenant, $feature) {
            return $tenant->subscription()
                ->addPrice($feature->price_id);
        }, false);
    }
}
