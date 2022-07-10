<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

\Illuminate\Support\Facades\Route::get('/test', function () {
	tenancy()->initialize(\App\Models\Tenant::first());
	return new \App\Mail\NewTenantMail(tenant(), \App\Models\User::first(), \Illuminate\Support\Str::random());
});