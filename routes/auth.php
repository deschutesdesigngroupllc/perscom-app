<?php

use App\Http\Controllers\Login\Social\LoginController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'feature:App\Features\SocialLoginFeature'], function () {
    Route::get('/{driver}/{function}/{tenant}/redirect', [LoginController::class, 'redirect'])
        ->name('social.redirect');
    Route::get('/{driver}/callback', [LoginController::class, 'callback'])
        ->name('social.callback');
});
