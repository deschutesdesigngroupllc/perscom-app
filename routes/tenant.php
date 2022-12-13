<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\Passport\AuthorizationController;
use App\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\ApproveAuthorizationController;
use Laravel\Passport\Http\Controllers\DenyAuthorizationController;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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
Route::group(['middleware' => [InitializeTenancyByDomainOrSubdomain::class, 'web']], function () {
    // Forms
    Route::group(['prefix' => 'forms'], function () {
        Route::get('{slug}', [FormController::class, 'index'])->name('form');
        Route::post('process', [FormController::class, 'process'])->name('form.process');
    });

    // Impersonation
    Route::get('/impersonate/{token}', function ($token) {
        return UserImpersonation::makeResponse($token);
    })->name('impersonate.tenant');

    // Socialite
    Route::group(['prefix' => 'auth'], function () {
        Route::get('/{driver}/redirect', [SocialLoginController::class, 'tenant'])->middleware('feature:social-login')
             ->name('auth.social.tenant.redirect');
        Route::get('/login/{loginToken}', [SocialLoginController::class, 'login'])->middleware('feature:social-login')
             ->name('auth.social.tenant.login');
    });

    // Authenticated Routes
    Route::group(['middleware' => 'auth'], function () {
        // Passport
        Route::group(['prefix' => 'oauth', 'middleware' => PreventAccessFromCentralDomains::class], function () {
            Route::get('/authorize', [AuthorizationController::class, 'authorize'])
                 ->name('passport.authorizations.authorize');
            Route::post('/authorize', [ApproveAuthorizationController::class, 'approve'])
                 ->name('passport.authorizations.approve');
            Route::delete('/authorize', [DenyAuthorizationController::class, 'deny'])
                 ->name('passport.authorizations.deny');
        });
    });
});
