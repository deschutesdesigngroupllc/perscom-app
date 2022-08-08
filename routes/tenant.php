<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Features\UserImpersonation;

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

// Initialize tenancy
Route::group(['middleware' => [InitializeTenancyByDomainOrSubdomain::class, 'web']], function () {
	Route::get('/forms/{slug}', function ($slug) {
		$form = \App\Models\Forms\Form::where('slug', $slug)->firstOrFail();
		return redirect()->route('nova.pages.create', [
			'resource' => \App\Nova\Forms\Submission::uriKey()
		]);
	})->name('form');

	Route::get('/impersonate/{token}', function ($token) {
		return UserImpersonation::makeResponse($token);
	})->middleware();
});