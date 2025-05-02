<?php

declare(strict_types=1);

use App\Http\Controllers\Oidc\DiscoveryController;
use App\Http\Controllers\Oidc\LogoutController;
use App\Http\Controllers\Oidc\UserInfoController;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::group(['middleware' => [InitializeTenancyBySubdomain::class, PreventAccessFromCentralDomains::class]], static function (): void {
    Route::group(['middleware' => 'web'], static function (): void {
        Route::get('.well-known/openid-configuration', [DiscoveryController::class, 'index'])
            ->name('discovery');

        Route::group(['prefix' => 'oauth', 'middleware' => ['auth:web,api', 'subscribed']], static function (): void {
            Route::get('logout', [LogoutController::class, 'index'])
                ->name('logout');
        });
    });

    Route::group(['prefix' => 'oauth', 'middleware' => ['api', 'auth:api', 'subscribed', 'scope:openid']], static function (): void {
        Route::get('userinfo', [UserInfoController::class, 'index'])
            ->name('userinfo');
    });
});
