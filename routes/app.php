<?php

declare(strict_types=1);

use App\Filament\App\Pages\AccountRequiresApproval;
use App\Http\Controllers\App\Pages\EditorController;
use App\Http\Controllers\App\Pages\PreviewController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use Illuminate\Http\RedirectResponse;
use Stancl\Tenancy\Features\UserImpersonation;

Route::group(['middleware' => ['web', InitializeTenancyBySubdomain::class]], function (): void {
    Route::get('impersonate/{token}', fn ($token): RedirectResponse => UserImpersonation::makeResponse($token))->name('impersonation');

    Route::group(['middleware' => ['auth:web']], function (): void {
        Route::get('approval-required', AccountRequiresApproval::class)
            ->name('approval-required');

        Route::group(['prefix' => 'admin', 'as' => 'admin.'], function (): void {
            Route::post('pages/preview', PreviewController::class)->name('pages.preview');
            Route::group(['middleware' => [HandleInertiaRequests::class]], function (): void {
                Route::get('pages/{page}/editor', [EditorController::class, 'index'])->name('pages.index');
                Route::post('pages/{page}/editor', [EditorController::class, 'store'])->name('pages.store');
            });
        });
    });
});
