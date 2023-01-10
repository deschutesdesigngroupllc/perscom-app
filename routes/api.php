<?php

use App\Http\Controllers\Api\V1\UnitsController;
use App\Http\Controllers\Api\V1\Users\UsersAssignmentRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersAwardRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersCombatRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersController;
use App\Http\Controllers\Api\V1\Users\UsersPositionController;
use App\Http\Controllers\Api\V1\Users\UsersQualificationRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersRankController;
use App\Http\Controllers\Api\V1\Users\UsersRankRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersServiceRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersSpecialtyController;
use App\Http\Controllers\Api\V1\Users\UsersStatusController;
use App\Http\Controllers\Api\V1\Users\UsersUnitController;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
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
        'treblle'
    ],
    'as' => 'api.'
], static function () {
    Route::apiResource('units', UnitsController::class);
    Route::get('users/me', [UsersController::class, 'me'])->name('users.me');

    // Users
    Orion::resource('users', UsersController::class);
    Orion::hasManyResource('users', 'assignment-records', UsersAssignmentRecordsController::class);
    Orion::hasManyResource('users', 'award-records', UsersAwardRecordsController::class);
    Orion::hasManyResource('users', 'combat-records', UsersCombatRecordsController::class);
    Orion::hasManyResource('users', 'qualification-records', UsersQualificationRecordsController::class);
    Orion::hasManyResource('users', 'rank-records', UsersRankRecordsController::class);
    Orion::hasManyResource('users', 'service-records', UsersServiceRecordsController::class);
    Orion::hasOneResource('users', 'position', UsersPositionController::class);
    Orion::hasOneResource('users', 'rank', UsersRankController::class);
    Orion::hasOneResource('users', 'specialty', UsersSpecialtyController::class);
    Orion::hasOneResource('users', 'status', UsersStatusController::class);
    Orion::belongsToResource('users', 'unit', UsersUnitController::class);
});
