<?php

namespace Tests\Central\Feature\Http\Controllers;

use App\Models\LoginToken;
use App\Models\User;
use App\Repositories\TenantRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Stancl\Tenancy\Contracts\Tenant;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\Central\CentralTestCase;

class SocialLoginControllerTest extends CentralTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
    }

    public function test_social_tenant_page_can_be_reached()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigit();

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('getTenantKey')->once()->andReturn($id);

        $this->instance(Tenant::class, $tenant);

        $this->get("http://test.localhost/auth/$driver/redirect")
            ->assertRedirectToRoute('auth.social.redirect', [
                'driver' => $driver,
                'tenant' => $id,
            ]);
    }

    public function test_social_redirect_page_can_be_reached()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigitNotZero();
        $redirect = $this->faker->url;

        $provider = $this->mock(Provider::class);
        $provider->allows('redirect')->once()->andReturn(new RedirectResponse($redirect));

        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);

        $this->get(config('app.auth_url')."/$driver/$id/redirect")
            ->assertRedirect($redirect)
            ->assertSessionHas('auth.social.login.tenant', $id);
    }

    public function test_social_callback_page_can_be_reached()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigit();
        $url = $this->faker->url;
        $token = Str::random();

        $user = $this->mock(User::class);

        $provider = $this->mock(Provider::class);
        $provider->allows('user')->once()->andReturn($user);

        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);

        $loginToken = $this->mock(LoginToken::class);
        $loginToken->allows('getAttribute')->with('token')->andReturn($token);

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('run')->once()->andReturn($loginToken);
        $tenant->allows('getAttribute')->with('url')->andReturn($url);

        $this->instance(\App\Models\Tenant::class, $tenant);

        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->allows('findById')->with($id)->andReturn($tenant);

        $this->instance(TenantRepository::class, $tenantRepository);

        $this->withSession([
            'auth.social.login.tenant' => $id,
        ])
            ->get(config('app.auth_url')."/$driver/callback")
            ->assertRedirect("$url/auth/login/$token")
            ->assertSessionMissing('auth.social.login.tenant', $id);
    }

    public function test_soclai_login_page_can_be_reached()
    {
        $id = $this->faker->randomDigitNotZero();
        $url = $this->faker->url;
        $token = Str::random();

        $loginToken = $this->mock(LoginToken::class);
        $loginToken->allows('getAttribute')->with('created_at')->andReturn(now());
        $loginToken->allows('getAttribute')->with('user_id')->andReturn($id);
        $loginToken->allows('delete')->andReturnNull();

        $this->instance(LoginToken::class, $loginToken);

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('getAttribute')->with('url')->andReturn($url);

        $this->instance(Tenant::class, $tenant);

        Auth::shouldReceive('loginUsingId')->with($id);

        $this->get("http://test.localhost/auth/login/$token")
            ->assertRedirect($url);
    }
}
