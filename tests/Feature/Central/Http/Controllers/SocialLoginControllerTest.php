<?php

namespace Tests\Feature\Central\Http\Controllers;

use App\Models\LoginToken;
use App\Models\User;
use App\Repositories\TenantRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Stancl\Tenancy\Contracts\Tenant;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\Feature\Central\CentralTestCase;

class SocialLoginControllerTest extends CentralTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
    }

    public function test_social_tenant_login_page_can_be_reached()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigit();

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('getTenantKey')->once()->andReturn($id);

        $this->instance(Tenant::class, $tenant);

        $this->get("http://test.localhost/auth/$driver/login/redirect")
            ->assertRedirectToRoute('auth.social.redirect', [
                'driver' => $driver,
                'function' => 'login',
                'tenant' => $id,
            ]);
    }

    public function test_social_tenant_register_page_can_be_reached()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigit();

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('getTenantKey')->once()->andReturn($id);

        $this->instance(Tenant::class, $tenant);

        $this->get("http://test.localhost/auth/$driver/register/redirect")
            ->assertRedirectToRoute('auth.social.redirect', [
                'driver' => $driver,
                'function' => 'register',
                'tenant' => $id,
            ]);
    }

    public function test_social_login_redirect_page_can_be_reached()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigitNotZero();
        $redirect = $this->faker->url;

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('getTenantKey')->once()->andReturn($id);

        $this->instance(\App\Models\Tenant::class, $tenant);

        $provider = $this->mock(Provider::class);
        $provider->allows('redirect')->once()->andReturn(new RedirectResponse($redirect));

        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);

        URL::forceRootUrl(config('app.auth_url'));

        $this->get(route('auth.social.redirect', [
            'driver' => $driver,
            'function' => 'login',
            'tenant' => $id,
        ]))
            ->assertRedirect($redirect)
            ->assertSessionHas('auth.social.login.tenant', [
                'tenant' => $id,
                'function' => 'login',
            ]);
    }

    public function test_social_register_redirect_page_can_be_reached()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigitNotZero();
        $redirect = $this->faker->url;

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('getTenantKey')->once()->andReturn($id);

        $this->instance(\App\Models\Tenant::class, $tenant);

        $provider = $this->mock(Provider::class);
        $provider->allows('redirect')->once()->andReturn(new RedirectResponse($redirect));

        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);

        URL::forceRootUrl(config('app.auth_url'));

        $this->get(route('auth.social.redirect', [
            'driver' => $driver,
            'function' => 'register',
            'tenant' => $id,
        ]))
            ->assertRedirect($redirect)
            ->assertSessionHas('auth.social.login.tenant', [
                'tenant' => $id,
                'function' => 'register',
            ]);
    }

    public function test_social_login_callback_page_can_be_reached()
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
        $tenant->allows('run')->times(3)->andReturn($user, $user, $loginToken);
        $tenant->allows('getAttribute')->with('url')->andReturn($url);
        $tenant->allows('route')->andReturn("$url/auth/login/$token");

        $this->instance(\App\Models\Tenant::class, $tenant);

        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->allows('findById')->with($id)->andReturn($tenant);

        $this->instance(TenantRepository::class, $tenantRepository);

        URL::forceRootUrl(config('app.auth_url'));

        $this->withSession([
            'auth.social.login.tenant' => [
                'tenant' => $id,
                'function' => 'login',
            ],
        ])
            ->get(route('auth.social.callback', [
                'driver' => $driver,
            ]))
            ->assertRedirect("$url/auth/login/$token")
            ->assertSessionMissing('auth.social.login.tenant', $id);
    }

    public function test_social_register_callback_page_can_be_reached()
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
        $tenant->allows('run')->times(3)->andReturn(null, $user, $loginToken);
        $tenant->allows('getAttribute')->with('url')->andReturn($url);
        $tenant->allows('route')->andReturn("$url/auth/login/$token");

        $this->instance(\App\Models\Tenant::class, $tenant);

        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->allows('findById')->with($id)->andReturn($tenant);

        $this->instance(TenantRepository::class, $tenantRepository);

        URL::forceRootUrl(config('app.auth_url'));

        $this->withSession([
            'auth.social.login.tenant' => [
                'tenant' => $id,
                'function' => 'register',
            ],
        ])
            ->get(route('auth.social.callback', [
                'driver' => $driver,
            ]))
            ->assertRedirect("$url/auth/login/$token")
            ->assertSessionMissing('auth.social.login.tenant', $id);
    }

    public function test_social_login_with_no_user_is_redirected()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigit();
        $url = $this->faker->url;

        $user = $this->mock(User::class);

        $provider = $this->mock(Provider::class);
        $provider->allows('user')->once()->andReturn($user);

        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('run')->once()->andReturnNull();
        $tenant->allows('getAttribute')->with('url')->andReturn($url);
        $tenant->allows('route')->andReturn("$url/login");

        $this->instance(\App\Models\Tenant::class, $tenant);

        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->allows('findById')->with($id)->andReturn($tenant);

        $this->instance(TenantRepository::class, $tenantRepository);

        URL::forceRootUrl(config('app.auth_url'));

        $this->withSession([
            'auth.social.login.tenant' => [
                'tenant' => $id,
                'function' => 'login',
            ],
        ])
            ->get(route('auth.social.callback', [
                'driver' => $driver,
            ]))
            ->assertRedirectContains("$url/login")
            ->assertSessionMissing('auth.social.login.tenant', $id);
    }

    public function test_social_register_with_previous_user_is_redirected()
    {
        $driver = $this->faker->word();
        $id = $this->faker->randomDigit();
        $url = $this->faker->url;
        $token = Str::random();
        $email = $this->faker->email;

        $user = $this->mock(User::class);
        $user->allows('getAttribute')->with('email')->andReturn($email);

        $provider = $this->mock(Provider::class);
        $provider->allows('user')->once()->andReturn($user);

        Socialite::shouldReceive('driver')->with($driver)->andReturn($provider);

        $tenant = $this->mock(\App\Models\Tenant::class);
        $tenant->allows('run')->once()->andReturn($user);
        $tenant->allows('getAttribute')->with('url')->andReturn($url);
        $tenant->allows('route')->andReturn("$url/login");

        $this->instance(\App\Models\Tenant::class, $tenant);

        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->allows('findById')->with($id)->andReturn($tenant);

        $this->instance(TenantRepository::class, $tenantRepository);

        URL::forceRootUrl(config('app.auth_url'));

        $this->withSession([
            'auth.social.login.tenant' => [
                'tenant' => $id,
                'function' => 'register',
            ],
        ])
            ->get(route('auth.social.callback', [
                'driver' => $driver,
            ]))
            ->assertRedirectContains("$url/login")
            ->assertSessionMissing('auth.social.login.tenant', $id);
    }
}
