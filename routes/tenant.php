<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\SocialLoginController;
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
	Route::get('/forms/{slug}', [FormController::class, 'index'])
		->name('form');

	Route::get('/impersonate/{token}', function ($token) {
		return UserImpersonation::makeResponse($token);
	})->name('impersonate.tenant');

	Route::group(['prefix' => 'auth'], function () {
		Route::get('/{driver}/redirect', [SocialLoginController::class, 'tenant'])
			->middleware('feature:social-login')
			->name('auth.social.tenant.redirect');
		Route::get('/login/{token}', [SocialLoginController::class, 'login'])
			->middleware('feature:social-login')
			->name('auth.social.tenant.login');
	});
});