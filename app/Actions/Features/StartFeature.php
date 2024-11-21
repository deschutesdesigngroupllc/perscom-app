<?php

declare(strict_types=1);

namespace App\Actions\Features;

use App\Models\Feature;
use App\Models\Subscription;
use App\Models\Tenant;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Exceptions\SubscriptionUpdateFailure;

class StartFeature
{
    /**
     * @throws SubscriptionUpdateFailure|IncompletePayment
     */
    public static function handle(Tenant $tenant, Feature $feature): Subscription|bool
    {
        /** @var Subscription $subscription */
        $subscription = $tenant->subscription();

        if (blank($subscription)) {
            return false;
        }

        $price = match ($subscription->renewal_term) {
            'monthly' => $feature->monthly_id,
            'yearly' => $feature->yearly_id,
            default => null
        };

        if (is_null($price)) {
            return false;
        }

        return rescue(function () use ($subscription, $price) {
            return $subscription
                ->addPriceAndInvoice($price);
        }, false);
    }
}
