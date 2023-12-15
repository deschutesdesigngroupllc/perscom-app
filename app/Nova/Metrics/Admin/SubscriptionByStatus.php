<?php

namespace App\Nova\Metrics\Admin;

use Laravel\Cashier\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class SubscriptionByStatus extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Subscription::class, 'stripe_status')
            ->label(fn ($value) => match ($value) {
                null => 'None',
                default => ucfirst($value),
            })
            ->colors([
                'active' => '#16A34A',
                'incomplete' => '#FACC15',
                'incomplete_expired' => '#DC2626',
                'trialing' => '#2563EB',
                'past_due' => '#FACC15',
                'canceled' => '#DC2626',
                'unpaid' => '#DC2626',
            ]);
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'admin-subscription-by-status';
    }
}
