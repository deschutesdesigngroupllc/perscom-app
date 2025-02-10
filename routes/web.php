<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\FindMyOrganizationController;
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\Landing\PrivacyPolicyController;
use App\Http\Controllers\Landing\RegisterController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use Spatie\ResponseCache\Middlewares\CacheResponse;

Route::group(['middleware' => 'landing', CacheResponse::class], static function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('landing.home');

    Route::group(['prefix' => 'find-my-organization'], static function () {
        Route::get('/', [FindMyOrganizationController::class, 'index'])
            ->name('find-my-organization.index');
        Route::post('/', [FindMyOrganizationController::class, 'store'])
            ->name('find-my-organization.store')
            ->middleware('throttle:find-my-organization');
        Route::get('{tenant}', [FindMyOrganizationController::class, 'show'])
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
        Route::get('complete/{tenant}', [RegisterController::class, 'complete'])
            ->name('register.complete')
            ->middleware('signed');
    });
});

Route::redirect('/slack', config('services.slack.invite_link'))
    ->name('slack');

Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin', 'as' => 'admin.'], function () {
    Route::get('health', HealthCheckResultsController::class)
        ->name('health');
});
