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
    public function tenant($driver, $function)
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
    public function redirect($driver, $function, Tenant $tenant)
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
    public function callback($driver, TenantRepository $tenantRepository)
    {
        $sessionData = session()->get(self::$sessionKey, []);

        $tenantId = Arr::get($sessionData, 'tenant');
        $function = Arr::get($sessionData, 'function');

        $socialLiteUser = Socialite::driver($driver)->user();

        $tenant = $tenantRepository->findById($tenantId);
        $token = $tenant->run(function ($tenant) use ($socialLiteUser, $driver, $function) {
            switch ($function) {
                case self::SOCIAL_LOGIN:
                    $user = User::firstWhere('email', '=', $socialLiteUser->email);
                    $user?->update([
                        'social_id' => $socialLiteUser->id,
                        'social_driver' => $driver,
                        'social_token' => $socialLiteUser->token,
                        'social_refresh_token' => $socialLiteUser->refreshToken,
                    ]);
                    break;

                case self::SOCIAL_REGISTER:
                    $user = User::create([
                        'name' => $socialLiteUser->name,
                        'email' => $socialLiteUser->email,
                        'email_verified_at' => now(),
                        'social_id' => $socialLiteUser->id,
                        'social_driver' => $driver,
                        'social_token' => $socialLiteUser->token,
                        'social_refresh_token' => $socialLiteUser->refreshToken,
                    ]);
                    break;
            }

            if ($user) {
                $token = LoginToken::create([
                    'user_id' => $user->id,
                ]);
            }

            return $token ?? null;
        });

        session()->remove(self::$sessionKey);

        if (! $token) {
            $provider = Str::title($driver);
            $query = Arr::query([
                'status' => "We could not find an account associated with your $provider profile. Please create a new account to continue.",
            ]);

            return redirect()->to("{$tenant->url}/register?{$query}");
        }

        return redirect()->to("{$tenant->url}/auth/login/$token->token");
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(LoginToken $loginToken)
    {
        if ($loginToken->created_at->diffInSeconds(Carbon::now()) > self::$loginTokenTtl) {
            abort(403);
        }

        Auth::loginUsingId($loginToken->user_id);

        $loginToken->delete();

        return redirect(tenant()->url);
    }
}
