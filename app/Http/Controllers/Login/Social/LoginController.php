<?php

namespace App\Http\Controllers\Login\Social;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use App\Models\Tenant;
use App\Models\User;
use App\Repositories\TenantRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    public const SOCIAL_LOGIN = 'login';

    public const SOCIAL_REGISTER = 'register';

    protected static string $sessionKey = 'auth.social.login.tenant';

    public function redirect(string $driver, string $function, Tenant $tenant): RedirectResponse
    {
        session()->put(self::$sessionKey, [
            'tenant' => $tenant->getTenantKey(),
            'function' => $function,
        ]);

        return Socialite::driver($driver)->redirect();
    }

    public function callback(string $driver, TenantRepository $tenantRepository): RedirectResponse
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
            redirect()->to($tenant->route('sso.index', [
                'token' => $token->token,
            ])) : ($redirect ?? redirect()->to($tenant->url));
    }
}
