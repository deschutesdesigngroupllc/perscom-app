<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\SubscriptionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SubscriptionItem> $items
 * @property-read int|null $items_count
 * @property-read Tenant|null $owner
 * @property-read string|null $renewal_term
 * @property-read Tenant|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription active()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription canceled()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription ended()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription expiredTrial()
 * @method static \Laravel\Cashier\Database\Factories\SubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription incomplete()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription notOnGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription notOnTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription onGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription onTrial()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription pastDue()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription recurring()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStripeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
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

    /**
     * @return Attribute<?string, void>
     */
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

    /**
     * @return Attribute<?string, void>
     */
    public function renewalTerm(): Attribute
    {
        return Attribute::get(function (): ?string {
            $plans = collect(Spark::plans('tenant'))->mapWithKeys(fn (Plan $plan) => [$plan->id => $plan->interval]);

            return data_get($plans, $this->stripe_price);
        })->shouldCache();
    }
}
