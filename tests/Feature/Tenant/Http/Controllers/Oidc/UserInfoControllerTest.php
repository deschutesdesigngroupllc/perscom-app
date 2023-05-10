<?php

namespace Tests\Feature\Tenant\Http\Controllers\Oidc;

use Laravel\Passport\Passport;
use Tests\Feature\Tenant\Requests\Traits\MakesApiRequests;
use Tests\Feature\Tenant\TenantTestCase;

class UserInfoControllerTest extends TenantTestCase
{
    use MakesApiRequests;

    public function test_userinfo_endpoint_can_be_reached()
    {
        $this->withoutApiMiddleware();

        Passport::actingAs($this->user, [
            'openid', 'profile', 'email',
        ], 'passport');

        $response = $this->get($this->tenant->url.'/oauth/userinfo')
            ->assertSuccessful()
            ->assertJson([
                'sub' => $this->user->getAuthIdentifier(),
                'email' => $this->user->email,
                'name' => $this->user->name,
            ]);
    }
}
