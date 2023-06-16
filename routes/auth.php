<?php

use App\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'feature:App\Features\SocialLoginFeature'], function () {
    Route::get('/{driver}/{function}/{tenant}/redirect', [SocialLoginController::class, 'redirect'])
        ->name('social.redirect');
    Route::get('/{driver}/callback', [SocialLoginController::class, 'callback'])
        ->name('social.callback');
    Route::get('test', function () {
        return redirect()->to('http://deschutes.lvh.me/login')->with('message', 'this is a test');
    });
});
