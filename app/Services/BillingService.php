<?php

declare(strict_types=1);

namespace App\Services;

use App\Filament\App\Pages\Dashboard;
use App\Models\Tenant;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class BillingService
{
    /**
     * Determine if the tenant is in any trial state — either a generic
     * (pre-Stripe) trial driven by `trial_ends_at`, or a Stripe-managed
     * subscription trial.
     */
    public function onTrial(Tenant $tenant): bool
    {
        if ($tenant->onGenericTrial()) {
            return true;
        }

        if ($tenant->onTrial()) {
            return true;
        }

        // Creation-grace only applies to brand-new tenants who haven't yet
        // attempted any subscription. Once a subscription record exists —
        // even an incomplete or expired one — they're out of the grace
        // window and must complete payment.
        if ($tenant->subscriptions()->exists()) {
            return false;
        }

        $graceDays = (int) config('services.stripe.trial_grace_days', 0);

        return $graceDays > 0 && $tenant->created_at?->gt(now()->subDays($graceDays)) === true;
    }

    /**
     * Resolve the appropriate billing URL for a tenant: a Stripe Checkout
     * session for tenants without an active subscription, or the Stripe
     * billing portal for everyone else. Falls back to the dashboard URL if
     * Stripe is unreachable or misconfigured so the user is never stranded.
     */
    public function actionUrl(Tenant $tenant, ?string $returnUrl = null): string
    {
        $fallback = $returnUrl ?? Dashboard::getUrl();

        try {
            if (! $tenant->subscribed() && filled($this->defaultPriceId())) {
                return $this->checkoutUrl($tenant, $returnUrl);
            }

            return $this->portalUrl($tenant, $returnUrl);
        } catch (Throwable $throwable) {
            Log::error('Failed to resolve billing action URL.', [
                'tenant_id' => $tenant->getKey(),
                'exception' => $throwable,
            ]);

            return $fallback;
        }
    }

    /**
     * @throws Exception
     */
    public function checkoutUrl(Tenant $tenant, ?string $returnUrl = null): string
    {
        $returnUrl ??= Dashboard::getUrl();

        return (string) $tenant->newSubscription('default', $this->defaultPriceId())
            ->checkout([
                'success_url' => $returnUrl,
                'cancel_url' => $returnUrl,
            ])->asStripeCheckoutSession()->url;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function portalUrl(Tenant $tenant, ?string $returnUrl = null, array $options = []): string
    {
        return $tenant->billingPortalUrl($returnUrl ?? Dashboard::getUrl(), $options);
    }

    private function defaultPriceId(): ?string
    {
        $priceId = config('services.stripe.default_price_id');

        return filled($priceId)
            ? $priceId
            : null;
    }
}
