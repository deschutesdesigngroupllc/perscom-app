<?php

declare(strict_types=1);

use App\Filament\App\Pages\AccountRequiresApproval;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Features\UserImpersonation;

Route::group(['as' => 'tenant.', 'middleware' => ['web', InitializeTenancyBySubdomain::class]], function () {
    Route::get('/impersonate/{token}', function ($token) {
        return UserImpersonation::makeResponse($token);
    })->name('impersonation');

    Route::group(['middleware' => ['auth:web']], function () {
        Route::get('approval-required', AccountRequiresApproval::class)
            ->name('approval-required');
    });
});
