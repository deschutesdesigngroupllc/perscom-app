<?php

use App\Http\Controllers\Oidc\DiscoveryController;
use App\Http\Controllers\Oidc\LogoutController;
use App\Http\Controllers\Oidc\UserInfoController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::group(['middleware' => [InitializeTenancyByDomainOrSubdomain::class, PreventAccessFromCentralDomains::class]], static function () {

    Route::group(['middleware' => 'web'], static function () {
        Route::get('/.well-known/openid-configuration', [DiscoveryController::class, 'index'])->name('discovery');

        Route::group(['prefix' => 'oauth', 'middleware' => 'subscribed'], static function () {
            Route::group(['middleware' => 'auth'], static function () {
                Route::get('/logout', [LogoutController::class, 'index'])->name('logout');
            });
        });
    });

    Route::group(['prefix' => 'oauth', 'middleware' => ['api', 'subscribed']], static function () {
        Route::group(['middleware' => 'auth:api'], static function () {
            Route::group(['middleware' => 'scope:openid'], static function () {
                Route::post('/logout', [LogoutController::class, 'index'])->name('logout');
                Route::get('/userinfo', [UserInfoController::class, 'index'])->name('userinfo');
            });
        });
    });
});
