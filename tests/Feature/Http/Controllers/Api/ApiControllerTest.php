<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Middleware\InitializeTenancyByRequestData;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
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
        Passport::actingAs($this->user);

        $this->withMiddleware(InitializeTenancyByRequestData::class);

        $this->getJson('/me')
             ->assertServerError();
    }

    public function test_api_cannot_be_reached_without_api_access_feature()
    {
        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);

        $this->subscription->allows('active')->andReturn(true);

        $this->tenant->allows('onTrial')->andReturn(false);
        $this->tenant->allows('onGenericTrial')->andReturn(false);

        $this->withMiddleware('subscribed');

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->plan->options([]);

        $this->getJson('/me')
             ->assertStatus(402);
    }

//    public function test_api_can_be_reached_with_api_access_feature()
//    {
//        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);
//
//        $this->withMiddleware('subscribed');
//
//        Passport::actingAs($this->user, [
//            'view:user',
//        ]);
//
//        $this->getJson('/me')
//             ->assertSuccessful();
//    }

    public function test_api_cannot_be_reached_with_inactive_subscription()
    {
        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);

        $this->subscription->allows('active')->andReturn(false);

        $this->tenant->allows('onTrial')->andReturn(false);
        $this->tenant->allows('onGenericTrial')->andReturn(false);

        $this->withMiddleware('subscribed');

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertStatus(402);
    }

    public function test_api_can_be_reached_while_on_trial()
    {
        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(false);

        $this->subscription->allows('active')->andReturn(false);

        $this->tenant->allows('onTrial')->andReturn(true);
        $this->tenant->allows('onGenericTrial')->andReturn(true);

        $this->withMiddleware('subscribed');

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertSuccessful();
    }
}
