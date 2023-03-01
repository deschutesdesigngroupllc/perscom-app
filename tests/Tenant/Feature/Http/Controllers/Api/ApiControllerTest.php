<?php

namespace Tests\Tenant\Feature\Http\Controllers\Api;

use App\Http\Middleware\Subscribed;
use Laravel\Passport\Passport;

class ApiControllerTest extends ApiTestCase
{
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

        $this->getJson('/me', [
            'X-Perscom-Id' => null,
        ])->assertUnauthorized();
    }

    public function test_api_cannot_be_reached_with_incorrect_perscom_id()
    {
        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me', [
            'X-Perscom-Id' => $this->faker->randomDigitNot($this->tenant->getTenantKey()),
        ])->assertUnauthorized();
    }

    public function test_api_can_be_reached_with_perscom_id()
    {
        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertSuccessful();
    }

    public function test_api_cannot_be_reached_without_api_access_feature()
    {
        $this->withSubscription();

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertStatus(402);
    }

    public function test_api_can_be_reached_with_api_access_feature()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertSuccessful();
    }

    public function test_api_cannot_be_reached_with_incomplete_subscription()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'), 'incomplete');

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertStatus(402);
    }

    public function test_api_cannot_be_reached_with_incomplete_expired_subscription()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'), 'incomplete_expired');

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertStatus(402);
    }

    public function test_api_can_be_reached_while_on_trial()
    {
        $this->onTrial();

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertSuccessful();
    }

    public function test_api_can_be_reached_while_in_demo_mode()
    {
        config()->set('tenancy.demo_host', $this->domain->host);
        config()->set('tenancy.demo_id', $this->tenant->getTenantKey());

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertSuccessful();
    }
}
