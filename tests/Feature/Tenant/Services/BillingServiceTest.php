<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Facades\Billing;
use App\Filament\App\Pages\Dashboard;
use App\Models\Tenant;
use App\Services\BillingService;
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Tests\Feature\Tenant\TenantTestCase;
use Throwable;

class BillingServiceTest extends TenantTestCase
{
    public function test_on_trial_is_true_during_generic_trial(): void
    {
        $tenant = Tenant::factory()->make([
            'trial_ends_at' => now()->addDays(3),
        ]);

        $this->assertTrue(resolve(BillingService::class)->onTrial($tenant));
    }

    public function test_on_trial_is_true_within_creation_grace_period(): void
    {
        Config::set('services.stripe.trial_grace_days', 7);

        $tenant = Tenant::factory()->make([
            'trial_ends_at' => null,
            'created_at' => now()->subDays(3),
        ]);

        $this->assertTrue(resolve(BillingService::class)->onTrial($tenant));
    }

    public function test_on_trial_is_false_outside_trial_and_grace_period(): void
    {
        Config::set('services.stripe.trial_grace_days', 7);

        $tenant = Tenant::factory()->make([
            'trial_ends_at' => null,
            'created_at' => now()->subDays(30),
        ]);

        $this->assertFalse(resolve(BillingService::class)->onTrial($tenant));
    }

    public function test_on_trial_ignores_grace_period_when_disabled(): void
    {
        Config::set('services.stripe.trial_grace_days', 0);

        $tenant = Tenant::factory()->make([
            'trial_ends_at' => null,
            'created_at' => now()->subDay(),
        ]);

        $this->assertFalse(resolve(BillingService::class)->onTrial($tenant));
    }

    public function test_action_url_returns_checkout_url_for_unsubscribed_tenant(): void
    {
        Config::set('services.stripe.default_price_id', 'price_test_123');

        $tenant = FakeTenant::fake(subscribed: false);
        $service = new RecordingBillingService(checkoutUrl: 'https://checkout.stripe.test/session');

        $this->assertSame('https://checkout.stripe.test/session', $service->actionUrl($tenant));
        $this->assertSame(1, $service->checkoutCalls);
        $this->assertSame(0, $service->portalCalls);
    }

    public function test_action_url_returns_portal_url_for_subscribed_tenant(): void
    {
        Config::set('services.stripe.default_price_id', 'price_test_123');

        $tenant = FakeTenant::fake(subscribed: true);
        $service = new RecordingBillingService(portalUrl: 'https://billing.stripe.test/portal');

        $this->assertSame('https://billing.stripe.test/portal', $service->actionUrl($tenant));
        $this->assertSame(0, $service->checkoutCalls);
        $this->assertSame(1, $service->portalCalls);
    }

    public function test_action_url_uses_portal_when_default_price_is_missing(): void
    {
        Config::set('services.stripe.default_price_id');

        $tenant = FakeTenant::fake(subscribed: false);
        $service = new RecordingBillingService(portalUrl: 'https://billing.stripe.test/portal');

        $this->assertSame('https://billing.stripe.test/portal', $service->actionUrl($tenant));
        $this->assertSame(1, $service->portalCalls);
    }

    public function test_action_url_falls_back_to_dashboard_on_exception(): void
    {
        Config::set('services.stripe.default_price_id', 'price_test_123');

        $tenant = FakeTenant::fake(subscribed: false);
        $service = new RecordingBillingService(checkoutException: new RuntimeException('Stripe unreachable'));

        $this->assertSame(Dashboard::getUrl(), $service->actionUrl($tenant));
    }

    public function test_action_url_falls_back_to_explicit_return_url_on_exception(): void
    {
        Config::set('services.stripe.default_price_id', 'price_test_123');

        $tenant = FakeTenant::fake(subscribed: false);
        $service = new RecordingBillingService(checkoutException: new RuntimeException('Stripe unreachable'));

        $this->assertSame(
            'https://app.test/billing',
            $service->actionUrl($tenant, 'https://app.test/billing'),
        );
    }

    public function test_facade_resolves_to_singleton_service(): void
    {
        $this->assertSame(resolve(BillingService::class), Billing::getFacadeRoot());
        $this->assertSame(resolve(BillingService::class), resolve(BillingService::class));
    }

    public function test_facade_proxies_on_trial_to_service(): void
    {
        $tenant = Tenant::factory()->make([
            'trial_ends_at' => now()->addDays(5),
        ]);

        $this->assertTrue(Billing::onTrial($tenant));
    }
}

class FakeTenant extends Tenant
{
    public bool $fakeSubscribed = false;

    public static function fake(bool $subscribed): self
    {
        $tenant = new self;
        $tenant->fakeSubscribed = $subscribed;

        return $tenant;
    }

    public function subscribed($type = 'default', $price = null): bool
    {
        return $this->fakeSubscribed;
    }

    public function getKey(): int
    {
        return 1;
    }
}

class RecordingBillingService extends BillingService
{
    public int $checkoutCalls = 0;

    public int $portalCalls = 0;

    public function __construct(
        private readonly string $checkoutUrl = 'https://checkout.stripe.test/session',
        private readonly string $portalUrl = 'https://billing.stripe.test/portal',
        private readonly ?Throwable $checkoutException = null,
        private readonly ?Throwable $portalException = null,
    ) {}

    public function checkoutUrl(Tenant $tenant, ?string $returnUrl = null): string
    {
        $this->checkoutCalls++;

        if ($this->checkoutException instanceof Throwable) {
            throw $this->checkoutException;
        }

        return $this->checkoutUrl;
    }

    public function portalUrl(Tenant $tenant, ?string $returnUrl = null, array $options = []): string
    {
        $this->portalCalls++;

        if ($this->portalException instanceof Throwable) {
            throw $this->portalException;
        }

        return $this->portalUrl;
    }
}
