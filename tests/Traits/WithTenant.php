<?php

namespace Tests\Traits;

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;

trait WithTenant
{
    /**
     * @var Tenant
     */
    protected $tenant = null;

    /**
     * @var Domain
     */
    protected $domain = null;

    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var bool
     */
    protected $withSubscription = false;

    /**
     * @var Subscription
     */
    protected $subscription = null;

    /**
     * @var string
     */
    protected $subscriptionStatus = 'active';

    /**
     * @var null
     */
    protected $priceId = null;

    /**
     * @var bool
     */
    protected $onTrial = false;

    /**
     * @var null
     */
    protected $trialExpiresAt = null;

    /**
     * @return void
     *
     * @throws \Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById
     */
    protected function setUpTenancy()
    {
        $id = random_int(1, 1000);
        $token = ParallelTesting::token() ?: Str::random();
        $this->tenant = Tenant::factory()->state([
            'id' => $id,
            'name' => "Tenant {$id} Test {$token}",
            'tenancy_db_name' => "tenant_{$id}_test_{$token}_testing",
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

        tenancy()->initialize($this->tenant);

        URL::forceRootUrl($this->tenant->url);

        $this->user = User::factory()->create();
    }

    /**
     * @return void
     */
    protected function tearDownTenancy()
    {
        $this->tenant->delete();
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

    /**
     * @return void
     */
    public function withoutSubscription()
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
