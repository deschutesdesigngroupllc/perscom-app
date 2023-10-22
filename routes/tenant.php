<?php

use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\Tenant\AdminController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

Route::group(['as' => 'tenant.', 'middleware' => [InitializeTenancyByDomainOrSubdomain::class, 'web']], function () {
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'signed:relative'], function () {
        Route::get('receipts/{id}/download', [AdminController::class, 'downloadReceipt'])
            ->name('download.receipt');
    });

    Route::get('/impersonate/{token}', function ($token) {
        return UserImpersonation::makeResponse($token);
    })->name('impersonate');

    Route::group(['prefix' => 'auth', 'middleware' => 'feature:App\Features\SocialLoginFeature'], function () {
        Route::get('/{driver}/{function}/redirect', [SocialLoginController::class, 'tenant'])
            ->name('auth.social.redirect');
        Route::get('/login/{token}', [SocialLoginController::class, 'login'])
            ->name('auth.social.login');
    });
});
