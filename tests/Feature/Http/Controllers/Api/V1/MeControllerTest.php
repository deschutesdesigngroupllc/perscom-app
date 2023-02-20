<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Laravel\Passport\Passport;

class MeControllerTest extends ApiResourceTestCase
{
    public function test_me_endpoint_can_be_reached()
    {
        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson('/me')
             ->assertSuccessful();
    }

    public function test_me_endpoint_cannot_be_reached_without_proper_scope()
    {
        Passport::actingAs($this->user);

        $this->getJson('/me')
             ->assertForbidden();
    }
}
