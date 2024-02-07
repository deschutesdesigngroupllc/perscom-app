<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Features\SocialLoginFeature;
use App\Http\Controllers\SocialLoginController;
use App\Http\Responses\LoginResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Laravel\Pennant\Feature;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        if (Request::isCentralRequest()) {
            config()->set('fortify.prefix', '/admin');
            config()->set('fortify.features', []);
            config()->set('fortify.guard', 'admin');
            config()->set('fortify.passwords', 'admins');
            config()->set('fortify.home', '/admin'.RouteServiceProvider::HOME);
        }

        if (Request::isDemoMode()) {
            config()->set('fortify.features', []);
        }
    }

    public function boot(): void
    {
        Fortify::loginView(function () {
            return Inertia::render('auth/Login', [
                'status' => session('status') ?? request()->input('status'),
                'canResetPassword' => Route::has('password.request'),
                'canCreateAnAccount' => Route::has('register') && setting('registration_enabled', true),
                'demoMode' => Request::isDemoMode(),
                'adminMode' => Request::isCentralRequest(),
                'enableSocialLogin' => Feature::active(SocialLoginFeature::class),
                'googleLogin' => \route('tenant.auth.social.redirect', [
                    'driver' => 'google',
                    'function' => SocialLoginController::SOCIAL_LOGIN,
                ]),
                'discordLogin' => \route('tenant.auth.social.redirect', [
                    'driver' => 'discord',
                    'function' => SocialLoginController::SOCIAL_LOGIN,
                ]),
                'githubLogin' => \route('tenant.auth.social.redirect', [
                    'driver' => 'github',
                    'function' => SocialLoginController::SOCIAL_LOGIN,
                ]),
            ]);
        });
        Fortify::requestPasswordResetLinkView(function () {
            return Inertia::render('auth/ForgotPassword');
        });
        Fortify::resetPasswordView(function ($request) {
            return Inertia::render('auth/ResetPassword', [
                'email' => $request->email,
                'token' => $request->route('token'),
            ]);
        });
        Fortify::verifyEmailView(function () {
            return Inertia::render('auth/VerifyEmail');
        });
        Fortify::registerView(function () {
            return Inertia::render('auth/Register', [
                'status' => session('status') ?? request()->input('status'),
                'enableSocialLogin' => Feature::active(SocialLoginFeature::class),
                'googleLogin' => \route('tenant.auth.social.redirect', [
                    'driver' => 'google',
                    'function' => SocialLoginController::SOCIAL_LOGIN,
                ]),
                'discordLogin' => \route('tenant.auth.social.redirect', [
                    'driver' => 'discord',
                    'function' => SocialLoginController::SOCIAL_LOGIN,
                ]),
                'githubLogin' => \route('tenant.auth.social.redirect', [
                    'driver' => 'github',
                    'function' => SocialLoginController::SOCIAL_LOGIN,
                ]),
            ]);
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
