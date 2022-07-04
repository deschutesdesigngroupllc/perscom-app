<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Http\Middleware\Authorize;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

Route::get('/routes', function (Request $request) {
	return [
		'person' => \route('nova.pages.create', 'soldiers'),
		'assignment' => \route('nova.pages.create', 'assignment-records'),
		'service' => \route('nova.pages.create', 'service-records'),
		'combat' => \route('nova.pages.create', 'combat-records'),
		'promotion' => \route('nova.pages.create', 'rank-records'),
		'award' => \route('nova.pages.create', 'award-records')
	];
})->middleware([Authenticate::class, Authorize::class]);
