<?php

use App\Http\Controllers\Oidc\DiscoveryController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::group([
    'middleware' => [
        InitializeTenancyByDomainOrSubdomain::class,
        PreventAccessFromCentralDomains::class,
        'subscribed'
    ]], function () {
        Route::get('/.well-known/openid-configuration', [DiscoveryController::class, 'index']);
    }
);
