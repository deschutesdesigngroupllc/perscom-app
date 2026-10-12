<?php

declare(strict_types=1);

use App\Filament\App\Pages\AccountRequiresApproval;
use App\Filament\App\Pages\Auth\AcceptInvite;
use App\Http\Controllers\App\Calendars\CalendarFeedController;
use App\Http\Controllers\App\Calendars\PersonalFeedController;
use App\Http\Controllers\App\Events\CalendarController;
use App\Http\Controllers\App\Events\CheckInController;
use App\Http\Controllers\App\Integrations\DiscordConnectController;
use App\Http\Controllers\App\Integrations\InstallController;
use App\Http\Controllers\App\Integrations\SlackConnectController;
use App\Http\Controllers\App\Pages\EditorController;
use App\Http\Controllers\App\Pages\PreviewController;
use App\Http\Controllers\App\ServiceProfiles\ImageController;
use App\Http\Controllers\App\ServiceProfiles\ShowController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;

Route::group(['middleware' => ['web', InitializeTenancyBySubdomain::class]], function (): void {
    Route::get('impersonate/{token}', fn ($token): RedirectResponse => UserImpersonation::makeResponse($token))->name('impersonation');

    Route::get('invitation/{token}', AcceptInvite::class)
        ->middleware('throttle:invites')
        ->name('invites.accept');

    Route::group(['prefix' => 'u', 'as' => 'service-profiles.', 'middleware' => ['throttle:service-profiles']], function (): void {
        Route::get('{handle}', ShowController::class)->where('handle', '[a-z0-9-]+')->name('show');
        Route::get('{handle}/card.png', ImageController::class)->where('handle', '[a-z0-9-]+')->name('image');
    });

    Route::group(['prefix' => 'feeds/{token}', 'as' => 'calendar-feeds.', 'middleware' => ['throttle:calendar-feeds']], function (): void {
        Route::get('calendar.ics', PersonalFeedController::class)->name('personal');
        Route::get('calendars/{calendar}.ics', CalendarFeedController::class)->whereNumber('calendar')->name('calendar');
    });

    Route::group(['middleware' => ['auth:web']], function (): void {
        Route::get('approval-required', AccountRequiresApproval::class)
            ->name('approval-required');

        Route::group(['prefix' => 'integrations', 'as' => 'integrations.'], function (): void {
            Route::get('{provider}/install', InstallController::class)->whereIn('provider', ['discord', 'slack'])->name('install');
            Route::get('discord/connect', DiscordConnectController::class)->name('discord.connect');
            Route::get('slack/connect', SlackConnectController::class)->name('slack.connect');
        });

        Route::get('events/{event}/check-in', CheckInController::class)
            ->middleware('signed:relative')
            ->name('events.check-in');

        Route::get('events/{event}/calendar.ics', CalendarController::class)
            ->name('events.calendar');

        Route::group(['prefix' => 'admin', 'as' => 'admin.'], function (): void {
            Route::post('pages/preview', PreviewController::class)->name('pages.preview');
            Route::group(['middleware' => [HandleInertiaRequests::class]], function (): void {
                Route::get('pages/{page}/editor', [EditorController::class, 'index'])->name('pages.index');
                Route::post('pages/{page}/editor', [EditorController::class, 'store'])->name('pages.store');
            });
        });
    });
});
