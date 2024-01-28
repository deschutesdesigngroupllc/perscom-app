<?php

namespace Tests\Feature\Tenant\Http\Controllers\Oidc;

use Laravel\Passport\Passport;
use Tests\Feature\Tenant\TenantTestCase;
use Tests\Traits\MakesApiRequests;

class UserInfoControllerTest extends TenantTestCase
{
    use MakesApiRequests;

    public function test_userinfo_endpoint_can_be_reached()
    {
        $this->withoutApiMiddleware();

        Passport::actingAs($this->user, [
            'openid', 'profile', 'email',
        ], 'passport');

        $this->get(route('oidc.userinfo'))
            ->assertSuccessful()
            ->assertJson([
                'sub' => $this->user->getAuthIdentifier(),
                'email' => $this->user->email,
                'name' => $this->user->name,
            ]);
    }

    public function test_cannot_reach_userinfo_endpoint_without_openid_scope()
    {
        $this->withoutApiMiddleware();

        Passport::actingAs($this->user, [
            'profile', 'email',
        ], 'passport');

        $response = $this->get(route('oidc.userinfo'))
            ->assertForbidden();
    }

    public function test_userinfo_endpoint_does_not_return_email_scope()
    {
        $this->withoutApiMiddleware();

        Passport::actingAs($this->user, [
            'openid', 'profile',
        ], 'passport');

        $response = $this->get(route('oidc.userinfo'))
            ->assertSuccessful()
            ->assertJsonMissing(['email' => $this->user->email]);
    }

    public function test_userinfo_endpoint_does_not_return_profile_scope()
    {
        $this->withoutApiMiddleware();

        Passport::actingAs($this->user, [
            'openid', 'email',
        ], 'passport');

        $response = $this->get(route('oidc.userinfo'))
            ->assertSuccessful()
            ->assertJsonMissing(['name' => $this->user->name]);
    }
}
