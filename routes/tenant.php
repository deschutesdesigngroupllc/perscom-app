<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

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
Route::group(['middleware' => InitializeTenancyByDomainOrSubdomain::class], function () {
	Route::get('/forms/{slug}', function ($slug) {
		$form = \App\Models\Forms\Form::where('slug', $slug)->firstOrFail();
		return $form->name;
	})->name('form');

	Route::get('/test', function () {
		$submission = \App\Models\Forms\Submission::make([
			'user_id' => 1,
			'form_id' => 1,
			'data.1' => 'test 1',
			'data.2' => 'test 2',
			'data.3' => 'test 3'
		]);
//		$submission = \App\Models\Forms\Submission::first();
//		echo "<pre>"; debug_backtrace();
		dd($submission);
	});
});