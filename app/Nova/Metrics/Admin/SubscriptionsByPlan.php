<?php

namespace App\Nova\Metrics\Admin;

use Laravel\Cashier\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Spark\Spark;

class SubscriptionsByPlan extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Subscription::class, 'stripe_price')
            ->label(function ($value) {
                $plans = Spark::plans('tenant');

                $plan = $plans->first(function ($plan) use ($value) {
                    return $plan->id == $value;
                });

                return $plan->name ?? $value;
            });
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        //return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'admin-subscriptions-by-price';
    }
}
