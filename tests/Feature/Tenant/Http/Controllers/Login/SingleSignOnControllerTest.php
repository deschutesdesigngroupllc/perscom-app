<?php

namespace Tests\Feature\Tenant\Http\Controllers\Login;

use App\Features\SingleSignOnFeature;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Feature;
use Tests\Feature\Tenant\TenantTestCase;

class SingleSignOnControllerTest extends TenantTestCase
{
    public function test_it_will_return_an_error_when_no_jwt_is_used()
    {
        $this->postJson(route('sso.store'))
            ->assertUnauthorized();
    }

    public function test_it_will_return_an_error_when_the_feature_is_disabled()
    {
        Feature::define(SingleSignOnFeature::class, false);

        $token = Auth::guard('jwt')->login(User::factory()->create());

        $this->withToken($token)
            ->postJson(route('sso.store'))
            ->assertForbidden();
    }

    public function test_it_will_return_a_redirect_url()
    {
        Feature::define(SingleSignOnFeature::class, true);

        $token = Auth::guard('jwt')->login(User::factory()->create());

        $this->withToken($token)
            ->postJson(route('sso.store'))
            ->assertSuccessful();
    }

    public function test_redirect_url_will_sign_a_user_in()
    {
        Feature::define(SingleSignOnFeature::class, true);

        $user = User::factory()->create();

        $token = LoginToken::factory()->state([
            'user_id' => $user->getKey(),
        ])->create();

        $this->getJson(route('sso.index', [
            'token' => $token,
        ]))
            ->assertRedirect();

        $this->assertAuthenticated();
    }
}
