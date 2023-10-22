<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use App\Models\Tenant;
use App\Models\User;
use App\Repositories\TenantRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Login function
     */
    public const SOCIAL_LOGIN = 'login';

    /**
     * Register function
     */
    public const SOCIAL_REGISTER = 'register';

    /**
     * @var string
     */
    protected static $sessionKey = 'auth.social.login.tenant';

    /**
     * @var int
     */
    protected static $loginTokenTtl = 60;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('feature:App\Features\SocialLoginFeature');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tenant(string $driver, string $function)
    {
        return redirect()->route('auth.social.redirect', [
            'driver' => $driver,
            'function' => $function,
            'tenant' => tenant()->getTenantKey(),
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect(string $driver, string $function, Tenant $tenant)
    {
        session()->put(self::$sessionKey, [
            'tenant' => $tenant->getTenantKey(),
            'function' => $function,
        ]);

        return Socialite::driver($driver)->redirect();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function callback(string $driver, TenantRepository $tenantRepository)
    {
        $sessionData = session()->get(self::$sessionKey, []);

        $tenantId = Arr::get($sessionData, 'tenant');
        $function = Arr::get($sessionData, 'function');

        $socialLiteUser = Socialite::driver($driver)->user();

        $tenant = $tenantRepository->findById($tenantId);
        $user = $tenant->run(function ($tenant) use ($socialLiteUser) {
            return User::firstWhere('email', '=', $socialLiteUser->email);
        });

        $redirect = null;
        if ($function === self::SOCIAL_REGISTER) {
            if ($user) {
                $driver = Str::title($driver);
                $redirect = redirect()
                    ->to($tenant->route('login', [
                        'status' => "You already have an account registered with your $driver email. Please login to continue.",
                    ]));
            } else {
                $user = $tenant->run(function ($tenant) use ($socialLiteUser, $driver) {
                    return User::create([
                        'name' => $socialLiteUser->name,
                        'email' => $socialLiteUser->email,
                        'email_verified_at' => now(),
                        'social_id' => $socialLiteUser->id,
                        'social_driver' => $driver,
                        'social_token' => $socialLiteUser->token,
                        'social_refresh_token' => $socialLiteUser->refreshToken,
                    ]);
                });
            }
        }

        if ($function === self::SOCIAL_LOGIN) {
            if ($user) {
                $tenant->run(function ($tenant) use ($socialLiteUser, $driver, $user) {
                    $user->update([
                        'social_id' => $socialLiteUser->id,
                        'social_driver' => $driver,
                        'social_token' => $socialLiteUser->token,
                        'social_refresh_token' => $socialLiteUser->refreshToken,
                    ]);
                });
            } else {
                $driver = Str::title($driver);
                $redirect = redirect()
                    ->to($tenant->route('register', [
                        'status' => "We could not find an account associated with your $driver email. Please create a new account to continue.",
                    ]));
            }
        }

        session()->forget(self::$sessionKey);

        $token = null;
        if ($user && ! $redirect) {
            $token = $tenant->run(function ($tenant) use ($user) {
                return LoginToken::create([
                    'user_id' => $user->id,
                ]);
            });
        }

        return $token ?
            redirect()->to($tenant->route('tenant.auth.social.login', [
                'token' => $token->token,
            ])) : ($redirect ?? redirect()->to($tenant->url));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(LoginToken $token)
    {
        if ($token->created_at->diffInSeconds(Carbon::now()) > self::$loginTokenTtl) {
            abort(403);
        }

        Auth::loginUsingId($token->user_id);

        $token->delete();

        return redirect(tenant()->url);
    }
}
