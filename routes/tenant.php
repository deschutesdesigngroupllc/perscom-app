<?php

use App\Http\Controllers\Login\Social\RedirectController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

Route::group(['as' => 'tenant.', 'middleware' => [InitializeTenancyByDomainOrSubdomain::class]], function () {
    Route::group(['middleware' => 'web'], function () {
        Route::get('impersonate/{token}', function ($token) {
            return UserImpersonation::makeResponse($token);
        })->name('impersonate');

        Route::get('auth/{driver}/{function}/redirect', [RedirectController::class, 'index'])
            ->middleware('feature:App\Features\SocialLoginFeature')
            ->name('auth.social.redirect.index');
    });
});
