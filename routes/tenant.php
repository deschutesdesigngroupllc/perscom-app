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

use App\Notifications\Records\NewServiceRecord;
use Illuminate\Support\Facades\Notification;

\Illuminate\Support\Facades\Route::get('/test', function () {
	tenancy()->initialize(\App\Models\Tenant::first());
	$service = \App\Models\Records\Service::first();
	dd($service->person->users->contains(1));
});