<?php

use App\Http\Controllers\Api\V1\UnitsController;
use App\Http\Controllers\Api\V1\UsersController;
use App\Http\Middleware\LogApiRequests;
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
Route::group([
    'middleware' => [
        InitializeTenancyByDomainOrSubdomain::class,
        LogApiRequests::class,
        'auth:api',
    ],
], function () {
    Route::apiResource('units', UnitsController::class)->only('index');
    Route::get('users/me', [UsersController::class, 'me'])->name('users.me');
    Route::apiResource('users', UsersController::class)->only('index');
});
