<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\SocialLoginController;

Route::get('/{driver}/{tenant}/redirect', [SocialLoginController::class, 'redirect'])->name('auth.social.redirect');
Route::get('/{driver}/callback', [SocialLoginController::class, 'callback'])->name('auth.social.callback');