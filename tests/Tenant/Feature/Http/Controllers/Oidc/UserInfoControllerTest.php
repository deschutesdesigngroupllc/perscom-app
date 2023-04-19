<?php

namespace Tests\Tenant\Feature\Http\Controllers\Oidc;

use App\Http\Middleware\Subscribed;
use Laravel\Passport\Passport;
use Tests\Tenant\TenantTestCase;

class UserInfoControllerTest extends TenantTestCase
{
    public function test_userinfo_endpoint_can_be_reached()
    {
        $this->withoutMiddleware(Subscribed::class);

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
