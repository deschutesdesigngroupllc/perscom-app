<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\SubscriptionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Laravel\Cashier\Subscription as BaseSubscription;
use Laravel\Cashier\SubscriptionItem;
use Spark\Plan;
use Spark\Spark;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property int $tenant_id
 * @property string $type
 * @property string $stripe_id
 * @property string $stripe_status
 * @property string|null $stripe_price
 * @property int|null $quantity
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, SubscriptionItem> $items
 * @property-read int|null $items_count
 * @property-read Tenant|null $owner
 * @property-read string|null $renewal_term
 * @property-read string|null $stripe_url
 * @property-read Tenant|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription canceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription ended()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription expiredTrial()
 * @method static \Laravel\Cashier\Database\Factories\SubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription incomplete()
 * @method static Builder<static>|Subscription newModelQuery()
 * @method static Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notOnGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notOnTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription onGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription onTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription pastDue()
 * @method static Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription recurring()
 * @method static Builder<static>|Subscription whereCreatedAt($value)
 * @method static Builder<static>|Subscription whereEndsAt($value)
 * @method static Builder<static>|Subscription whereId($value)
 * @method static Builder<static>|Subscription whereQuantity($value)
 * @method static Builder<static>|Subscription whereStripeId($value)
 * @method static Builder<static>|Subscription whereStripePrice($value)
 * @method static Builder<static>|Subscription whereStripeStatus($value)
 * @method static Builder<static>|Subscription whereTenantId($value)
 * @method static Builder<static>|Subscription whereTrialEndsAt($value)
 * @method static Builder<static>|Subscription whereType($value)
 * @method static Builder<static>|Subscription whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(SubscriptionObserver::class)]
class Subscription extends BaseSubscription
{
    use CentralConnection;

    public function hasMultiplePrices(): bool
    {
        return true;
    }

    public function stripePrice(): Attribute
    {
        return Attribute::get(function ($value, $attributes = null): ?string {
            if (filled($value)) {
                return $value;
            }

            /** @var SubscriptionItem $item */
            $item = $this->items->sortBy('created_at')->firstWhere('subscription_id', data_get($attributes, 'id'));

            if (blank($item)) {
                return null;
            }

            return $item->stripe_price;
        })->shouldCache();
    }

    public function stripeUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (blank($this->stripe_id)) {
                return null;
            }

            return match (true) {
                App::isProduction() => 'https://dashboard.stripe.com/subscriptions/'.$this->stripe_id,
                default => 'https://dashboard.stripe.com/test/subscriptions/'.$this->stripe_id
            };
        });
    }

    public function renewalTerm(): Attribute
    {
        return Attribute::get(function (): ?string {
            $plans = collect(Spark::plans('tenant'))->mapWithKeys(fn (Plan $plan): array => [$plan->id => $plan->interval]);

            return data_get($plans, $this->stripe_price);
        })->shouldCache();
    }
}
