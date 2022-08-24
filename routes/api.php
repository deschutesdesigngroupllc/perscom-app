<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => [InitializeTenancyByDomainOrSubdomain::class, 'auth:api']], function () {
	Route::get('/me', function (Request $request) {
		return \App\Http\Resources\Api\MeResource::make($request->user());
	})->name('api.me');
	Route::get('/users', function () {
		return \App\Http\Resources\Api\UserResource::collection(\App\Models\User::all()->keyBy->id);
	})->name('api.users.index');
});

