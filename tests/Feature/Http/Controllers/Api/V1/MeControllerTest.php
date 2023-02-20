<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Laravel\Passport\Passport;
use Tests\Feature\Http\Controllers\Api\ApiTestCase;

class MeControllerTest extends ApiTestCase
{
    public function test_me_endpoint_can_be_reached()
    {
        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(true);

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me');
    }

    public function test_me_endpoint_cannot_be_reached_without_proper_scope()
    {
        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(true);

        Passport::actingAs($this->user);

        $this->getJson('/me')
             ->assertForbidden();
    }
}
