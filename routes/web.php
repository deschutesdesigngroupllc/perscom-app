<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\RegisterController;
use App\Http\Controllers\Landing\VerifyController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::group(['domain' => parse_url((string) config('app.url'), PHP_URL_HOST)], static function (): void {
    Route::get('/', static fn () => redirect()->away((string) config('app.landing_redirect_url'), 302))
        ->name('landing.redirect');

    Route::get('health', HealthCheckResultsController::class)
        ->middleware('auth:admin')
        ->name('health');

    Route::group(['middleware' => ['landing']], static function (): void {
        Route::group(['prefix' => 'register', 'middleware' => ['env:production,local']], static function (): void {
            Route::get('/', [RegisterController::class, 'index'])
                ->name('register.index');
            Route::post('/', [RegisterController::class, 'store'])
                ->name('register.store')
                ->middleware('throttle:register');
            Route::get('verify/{registration}', VerifyController::class)
                ->name('register.verify')
                ->middleware('signed');
            Route::get('complete', [RegisterController::class, 'show'])
                ->name('register.show')
                ->middleware('signed');
        });
    });

    Route::fallback(static fn () => redirect()->away((string) config('app.landing_redirect_url'), 302));
});

Route::redirect('/slack', config('services.slack.invite_link'))
    ->name('slack');
