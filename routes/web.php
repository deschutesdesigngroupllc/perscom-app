<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\FindMyOrganizationController;
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\Landing\RegisterController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use Spatie\ResponseCache\Middlewares\CacheResponse;

Route::group(['middleware' => 'landing', CacheResponse::class], static function (): void {
    Route::get('/', [HomeController::class, 'index'])
        ->name('landing.home');

    Route::group(['prefix' => 'find-my-organization'], static function (): void {
        Route::get('/', [FindMyOrganizationController::class, 'index'])
            ->name('find-my-organization.index');
        Route::post('/', [FindMyOrganizationController::class, 'store'])
            ->name('find-my-organization.store')
            ->middleware('throttle:find-my-organization');
        Route::get('{tenant}', [FindMyOrganizationController::class, 'show'])
            ->middleware('signed')
            ->name('find-my-organization.show');
    });

    Route::group(['prefix' => 'legal'], function () {
        Route::inertia('acceptable-use-policy', 'legal/AcceptableUsePolicy')
            ->name('acceptable-use-policy');

        Route::inertia('cookie-policy', 'legal/CookiePolicy')
            ->name('cookie-policy');

        Route::inertia('privacy-policy', 'legal/PrivacyPolicy')
            ->name('privacy-policy');

        Route::inertia('terms-of-service', 'legal/TermsOfService')
            ->name('terms-of-service');
    });

    Route::group(['prefix' => 'register'], static function (): void {
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

Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin', 'as' => 'admin.'], function (): void {
    Route::get('health', HealthCheckResultsController::class)
        ->name('health');
});
