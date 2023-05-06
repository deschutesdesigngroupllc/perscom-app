<?php

namespace Tests\Tenant\Feature\Http\Controllers\Oidc;

use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\SentryContext;
use App\Http\Middleware\Subscribed;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Laravel\Passport\Passport;
use Tests\Tenant\TenantTestCase;
use Treblle\Middlewares\TreblleMiddleware;

class UserInfoControllerTest extends TenantTestCase
{
    public function test_userinfo_endpoint_can_be_reached()
    {
        $this->withoutMiddleware([
            TreblleMiddleware::class,
            SentryContext::class,
            LogApiRequests::class,
            ThrottleRequests::class,
            Subscribed::class,
        ]);

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
