<?php

declare(strict_types=1);

use App\Http\Controllers\Mcp\AuthorizationServerMetadataController;
use App\Http\Controllers\Mcp\ProtectedResourceMetadataController;
use App\Http\Middleware\AuthenticateApi;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use App\Http\Middleware\PreventAccessFromCentralDomains;
use App\Mcp\Servers\PerscomServer;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Facades\Mcp;
use Laravel\Mcp\Server\Http\Controllers\OAuthRegisterController;

Route::group([
    'middleware' => [
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ],
], static function (): void {
    Route::get('.well-known/oauth-protected-resource/{path?}', ProtectedResourceMetadataController::class)
        ->where('path', '.*')
        ->name('mcp.oauth.protected-resource.nested');

    Route::get('.well-known/oauth-authorization-server/{path?}', AuthorizationServerMetadataController::class)
        ->where('path', '.*')
        ->name('mcp.oauth.authorization-server');

    Route::post('oauth/register', OAuthRegisterController::class)
        ->middleware('throttle:register')
        ->name('mcp.oauth.register');

    Mcp::web('mcp', PerscomServer::class)
        ->middleware([AuthenticateApi::class, 'throttle:api'])
        ->name('mcp.server');
});
