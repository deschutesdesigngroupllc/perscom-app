<?php

use App\Http\Controllers\Oidc\DiscoveryController;
use App\Http\Controllers\Oidc\UserInfoController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::group([
    'middleware' => [
        InitializeTenancyByDomainOrSubdomain::class,
        PreventAccessFromCentralDomains::class,
        'subscribed'
    ]], function () {
        Route::get('/.well-known/openid-configuration', [DiscoveryController::class, 'index']);

        Route::group(['middlware' => 'scope:openid'], function () {
            Route::get('/userinfo', [UserInfoController::class, 'index']);
        });
});
