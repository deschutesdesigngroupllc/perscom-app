<?php

use App\Http\Controllers\Login\SingleSignOnController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

Route::group(['middleware' => [InitializeTenancyByDomainOrSubdomain::class]], function () {
    Route::group(['middleware' => ['api', 'auth:jwt', 'feature:App\Features\SingleSignOnFeature']], function () {
        Route::post('login', [SingleSignOnController::class, 'redirect'])
            ->name('redirect');
    });

    Route::group(['middleware' => 'web'], function () {
        Route::get('login/{token}', [SingleSignOnController::class, 'login'])
            ->name('login');
    });
});
