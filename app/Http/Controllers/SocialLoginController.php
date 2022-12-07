<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * @var string
     */
    protected static $sessionKey = 'auth.social.login.tenant';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('feature:social-login');
    }

    /**
     * @param $driver
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tenant($driver)
    {
        return redirect()->route('auth.social.redirect', [
            'driver' => $driver,
            'tenant' => tenant()->getTenantKey(),
        ]);
    }

    /**
     * @param $driver
     * @param $tenant
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($driver, $tenant)
    {
        if (! $tenant) {
            throw new \RuntimeException('There was no Tenant ID included with your request. Please try again.');
        }

        session()->put(self::$sessionKey, $tenant);

        return Socialite::driver($driver)->redirect();
    }

    /**
     * @param $driver
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function callback($driver)
    {
        $tenantId = session()->get(self::$sessionKey);

        if (! $tenantId) {
            throw new \RuntimeException('There was no Tenant ID saved to your session. Please try again.');
        }

        $socialLiteUser = Socialite::driver($driver)->user();

        $tenant = Tenant::findOrFail($tenantId);
        $token = $tenant->run(function ($tenant) use ($socialLiteUser, $driver) {
            $user = User::updateOrCreate(
                [
                    'email' => $socialLiteUser->email,
                ],
                [
                    'name' => $socialLiteUser->name,
                    'email_verified_at' => now(),
                    'social_id' => $socialLiteUser->id,
                    'social_driver' => $driver,
                    'social_token' => $socialLiteUser->token,
                    'social_refresh_token' => $socialLiteUser->refreshToken,
                ]
            );

            return LoginToken::create([
                'user_id' => $user->id,
            ]);
        });

        return redirect()->to("{$tenant->url}/auth/login/$token->token");
    }

    /**
     * @param  LoginToken  $token
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(LoginToken $token)
    {
        Auth::loginUsingId($token->user_id);

        $token->delete();

        return redirect(tenant()->url);
    }
}
