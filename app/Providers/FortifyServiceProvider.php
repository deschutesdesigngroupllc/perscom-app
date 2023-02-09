<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
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

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::loginView(function () {
            return Inertia::render('auth/Login', [
                'status' => session('status'),
                'canResetPassword' => Route::has('password.request'),
                'demoMode' => Request::isDemoMode(),
                'enableSocialLogin' => ! Request::isCentralRequest() &&
                                       ! Request::isDemoMode() &&
                                       FeatureFlag::isOn('social-login'),
                'githubLogin' => \route('tenant.auth.social.redirect', [
                    'driver' => 'github',
                ]),
                'discordLogin' => \route('tenant.auth.social.redirect', [
                    'driver' => 'discord',
                ]),
            ]);
        });
        Fortify::requestPasswordResetLinkView(function () {
            return Inertia::render('auth/ForgotPassword', [
                'status' => session('status'),
            ]);
        });
        Fortify::resetPasswordView(function ($request) {
            return Inertia::render('auth/ResetPassword', [
                'email' => $request->email,
                'token' => $request->route('token'),
            ]);
        });
        Fortify::verifyEmailView(function () {
            return Inertia::render('auth/VerifyEmail', [
                'status' => session('status'),
            ]);
        });
        Fortify::registerView(function () {
            return Inertia::render('auth/Register', [
                'status' => session('status'),
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
