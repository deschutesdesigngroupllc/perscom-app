<?php

use App\Http\Controllers\FindMyOrganizationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Swagger\HomeController as SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('landing.home');

Route::get('documentation/api', [SwaggerController::class, 'index'])
    ->name('api.documentation');

Route::group(['prefix' => 'find-my-organization'], static function () {
    Route::get('/', [FindMyOrganizationController::class, 'index'])
        ->name('find-my-organization.index');
    Route::post('/', [FindMyOrganizationController::class, 'store'])
        ->name('find-my-organization.store')
        ->middleware('throttle:find-my-organization');
    Route::get('/{tenant}', [FindMyOrganizationController::class, 'show'])
        ->middleware('signed')
        ->name('find-my-organization.show');
});

Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])
    ->name('privacy-policy.index');

Route::group(['prefix' => 'register'], static function () {
    Route::get('/', [RegisterController::class, 'index'])
        ->name('register.index');
    Route::post('/', [RegisterController::class, 'store'])
        ->name('register.store')
        ->middleware('throttle:register');
    Route::get('/complete/{tenant}', [RegisterController::class, 'complete'])
        ->name('register.complete')
        ->middleware('signed');
});
