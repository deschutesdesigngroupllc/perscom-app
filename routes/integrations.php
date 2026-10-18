<?php

declare(strict_types=1);

use App\Http\Controllers\Integrations\DiscordInteractionsController;
use App\Http\Controllers\Integrations\InstallCallbackController;
use App\Http\Middleware\VerifyDiscordSignature;
use Illuminate\Support\Facades\Route;

Route::post('discord/interactions', DiscordInteractionsController::class)
    ->middleware(VerifyDiscordSignature::class)
    ->name('discord.interactions');

Route::get('{provider}/install/callback', InstallCallbackController::class)
    ->whereIn('provider', ['discord', 'slack'])
    ->name('install.callback');
