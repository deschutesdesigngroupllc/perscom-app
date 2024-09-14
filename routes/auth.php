<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\Social\CallbackController;
use App\Http\Controllers\Auth\Social\RedirectController;

Route::get('/{panel}/oauth/{provider}/{tenant}/redirect', RedirectController::class)
    ->name('social.redirect');

Route::get('/{provider}/callback', CallbackController::class)
    ->name('social.callback');
