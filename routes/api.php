<?php

use App\Http\Controllers\Api\V1\Announcements\AnnouncementsController;
use App\Http\Controllers\Api\V1\Awards\AwardsController;
use App\Http\Controllers\Api\V1\Forms\SubmissionsController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\Qualifications\QualificationsController;
use App\Http\Controllers\Api\V1\Ranks\RanksController;
use App\Http\Controllers\Api\V1\Roster\RosterController;
use App\Http\Controllers\Api\V1\SpecController;
use App\Http\Controllers\Api\V1\Units\UnitsController;
use App\Http\Controllers\Api\V1\Units\UnitsUsersController;
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
use App\Http\Middleware\InitializeTenancyByRequestData;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
Route::group(['prefix' => 'v1'], static function () {
    // Spec
    Route::get('spec.yaml', [SpecController::class, 'index'])->name('spec');

    // Tenant
    Route::group([
        'middleware' => [
            'auth:api',
            'treblle',
            InitializeTenancyByRequestData::class,
            LogApiRequests::class,
            'subscribed',
        ],
    ], static function () {
        // OIDC
        Orion::resource('me', MeController::class)->only('index');

        // Announcements
        Orion::resource('announcements', AnnouncementsController::class);

        // Awards
        Orion::resource('awards', AwardsController::class);

        // Qualifications
        Orion::resource('qualifications', QualificationsController::class);

        // Ranks
        Orion::resource('ranks', RanksController::class);

        // Roster
        Orion::resource('roster', RosterController::class);

        // Submissions
        Orion::resource('submissions', SubmissionsController::class);

        // Units
        Orion::resource('units', UnitsController::class);
        Orion::hasManyResource('units', 'users', UnitsUsersController::class);

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

    // Route not found
    Route::fallback(function () {
        throw new NotFoundHttpException('The requested API endpoint could not be found or you do not have access to it.');
    })->name('error');
});
