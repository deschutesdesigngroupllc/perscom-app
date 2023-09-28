<?php

namespace Tests\Feature\Tenant\Requests\Traits;

use App\Models\Admin;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

trait WithTenant
{
    protected ?Tenant $tenant = null;

    protected ?Domain $domain = null;

    protected ?User $user = null;

    protected ?Admin $admin = null;

    protected bool $withSubscription = false;

    protected ?Subscription $subscription = null;

    protected string $subscriptionStatus = 'active';

    protected ?string $priceId = null;

    protected bool $onTrial = false;

    protected ?CarbonInterface $trialExpiresAt = null;

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Exception
     */
    protected function setUpTenancy(): void
    {
        if (method_exists($this, 'beforeSetUpTenancy')) {
            $this->beforeSetUpTenancy();
        }

        $this->admin = Admin::factory()->create();

        $id = random_int(1, 1000000);
        $token = $this->getTestToken();

        $tenantName = "Tenant {$id} Test {$token}";
        $tenantDatabaseName = "tenant_{$id}_test_{$token}_testing";

        Log::debug('Creating tenant', [
            'id' => $id,
            'token' => $token,
            'tenantName' => $tenantName,
            'tenantDatabaseName' => $tenantDatabaseName,
        ]);

        $this->tenant = Tenant::factory()->state([
            'id' => $id,
            'name' => $tenantName,
            'tenancy_db_name' => $tenantDatabaseName,
        ])->create();

        $this->domain = Domain::factory()->state([
            'domain' => "tenant{$id}test{$token}",
            'tenant_id' => $this->tenant->getKey(),
        ])->create();

        if ($this->withSubscription) {
            $priceId = $this->priceId ?? env('STRIPE_PRODUCT_BASIC_MONTH');
            $this->withSubscription($priceId, $this->subscriptionStatus, $this->trialExpiresAt);
        }

        if ($this->onTrial) {
            $this->onTrial($this->trialExpiresAt);
        } else {
            $this->tenant->forceFill([
                'trial_ends_at' => null,
            ])->save();
        }

        if (method_exists($this, 'beforeInitializingTenancy')) {
            $this->beforeInitializingTenancy();
        }

        tenancy()->initialize($this->tenant);
        tenant()->load('domains');

        URL::forceRootUrl($this->tenant->url);

        $this->user = User::factory()->create();
    }

    protected function tearDownTenancy(): void
    {
        if (method_exists($this, 'beforeTearDownTenant')) {
            $this->beforeTearDownTenant();
        }

        $this->admin->deleteQuietly();
        $this->tenant->deleteQuietly();
    }

    public function withSubscription(string|int $priceId = null, string $subscriptionStatus = 'active', $trialExpiresAt = null): void
    {
        $priceId = $priceId ?? env('STRIPE_PRODUCT_BASIC_MONTH');

        $this->withoutSubscription();

        $this->subscription = $this->tenant->subscriptions()->create([
            'name' => 'default',
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
