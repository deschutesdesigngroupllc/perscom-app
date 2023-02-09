<?php

use App\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/{driver}/{tenant}/redirect', [SocialLoginController::class, 'redirect'])
     ->middleware('feature:social-login')
     ->name('social.redirect');
Route::get('/{driver}/callback', [SocialLoginController::class, 'callback'])
     ->middleware('feature:social-login')
     ->name('social.callback');
