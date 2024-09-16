<?php

declare(strict_types=1);

use App\Http\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Features\UserImpersonation;

Route::group(['as' => 'tenant.', 'middleware' => ['web', InitializeTenancyBySubdomain::class]], function () {
    Route::get('/impersonate/{token}', function ($token) {
        return UserImpersonation::makeResponse($token);
    })->name('impersonation');
});
