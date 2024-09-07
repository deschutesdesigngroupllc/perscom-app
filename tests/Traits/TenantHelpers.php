<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;

trait TenantHelpers
{
    protected ?Subscription $subscription = null;

    public function withSubscription(string|int|null $priceId = null, string $subscriptionStatus = 'active', $trialExpiresAt = null): void
    {
        $priceId = $priceId ?? env('STRIPE_PRODUCT_BASIC_MONTH');

        $this->withoutSubscription();

        $this->subscription = $this->tenant->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => Str::random(10),
            'stripe_status' => $subscriptionStatus,
            'stripe_price' => $priceId,
            'quantity' => 1,
            'trial_ends_at' => $trialExpiresAt,
            'ends_at' => null,
        ]);

        $this->subscription->items()->create([
            'stripe_id' => Str::random(10),
            'stripe_product' => Str::random(10),
            'stripe_price' => $priceId,
            'quantity' => 1,
        ]);
    }

    public function withoutSubscription(): void
    {
        $this->tenant->subscriptions()->delete();
    }

    public function onTrial($trialExpiresAt = null): void
    {
        $this->tenant->forceFill([
            'trial_ends_at' => $trialExpiresAt ?? Carbon::now()->addDays(7),
        ])->save();
    }
}
