<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Middleware\InitializeTenancyByRequestData;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Laravel\Passport\Passport;
use Tests\Traits\WithTenant;

class ApiControllerTest extends ApiTestCase
{
    use WithTenant;

    public function test_api_cannot_be_reached_without_bearer_token()
    {
        $this->getJson('/me')
             ->assertUnauthorized();
    }

    public function test_api_cannot_be_reached_without_perscom_id()
    {
        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->withMiddleware(InitializeTenancyByRequestData::class);

        $this->getJson('/me')
             ->assertUnauthorized();
    }

    public function test_api_cannot_be_reached_with_perscom_id()
    {
        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(true);

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->withMiddleware(InitializeTenancyByRequestData::class);

        $this->getJson('/me', [
            'X-Perscom-Id' => $this->tenant->getTenantKey(),
        ])->assertSuccessful();
    }

    public function test_api_cannot_be_reached_without_api_access_feature()
    {
        $this->withSubscription();

        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);

        $this->withMiddleware('subscribed');

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertStatus(402);
    }

    public function test_api_can_be_reached_with_api_access_feature()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);

        $this->withMiddleware('subscribed');

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertSuccessful();
    }

    public function test_api_cannot_be_reached_with_incomplete_subscription()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'), 'incomplete');

        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);

        $this->withMiddleware('subscribed');

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertStatus(402);
    }

    public function test_api_cannot_be_reached_with_incomplete_expired_subscription()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'), 'incomplete_expired');

        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);

        $this->withMiddleware('subscribed');

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertStatus(402);
    }

    public function test_api_can_be_reached_while_on_trial()
    {
        $this->onTrial();

        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);

        $this->withMiddleware('subscribed');

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertSuccessful();
    }
}
