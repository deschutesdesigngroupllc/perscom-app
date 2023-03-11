<?php

use App\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Initialize tenancy
Route::group(['as' => 'tenant.', 'middleware' => [InitializeTenancyByDomainOrSubdomain::class, 'web']], function () {
    // Impersonation
    Route::get('/impersonate/{token}', function ($token) {
        return UserImpersonation::makeResponse($token);
    })->name('impersonate');

    // Socialite
    Route::group(['prefix' => 'auth', 'middleware' => 'feature:App\Features\SocialLoginFeature'], function () {
        Route::get('/{driver}/redirect', [SocialLoginController::class, 'tenant'])
             ->name('auth.social.redirect');
        Route::get('/login/{loginToken}', [SocialLoginController::class, 'login'])
             ->name('auth.social.login');
    });
});
