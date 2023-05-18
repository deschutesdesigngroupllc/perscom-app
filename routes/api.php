<?php

use App\Http\Controllers\Api\V1\Announcements\AnnouncementsController;
use App\Http\Controllers\Api\V1\Awards\AwardsController;
use App\Http\Controllers\Api\V1\Calendars\CalendarsController;
use App\Http\Controllers\Api\V1\Calendars\CalendarsEventsController;
use App\Http\Controllers\Api\V1\Calendars\EventsController;
use App\Http\Controllers\Api\V1\Forms\FormsController;
use App\Http\Controllers\Api\V1\Forms\FormsSubmissionsController;
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
use App\Http\Controllers\Api\V1\Users\UsersSecondaryPositionsController;
use App\Http\Controllers\Api\V1\Users\UsersSecondarySpecialtiesController;
use App\Http\Controllers\Api\V1\Users\UsersSecondaryUnitsController;
use App\Http\Controllers\Api\V1\Users\UsersServiceRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersSpecialtyController;
use App\Http\Controllers\Api\V1\Users\UsersStatusController;
use App\Http\Controllers\Api\V1\Users\UsersUnitController;
use App\Http\Middleware\InitializeTenancyByRequestData;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
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
    Route::get('spec.yaml', [SpecController::class, 'index'])->name('spec');

    Route::group([
        'middleware' => [
            'auth:api',
            InitializeTenancyByRequestData::class,
            PreventAccessFromCentralDomains::class,
            'subscribed',
        ],
    ], static function () {
        Orion::resource('me', MeController::class)->only('index');
        Orion::resource('announcements', AnnouncementsController::class);

        Orion::resource('awards', AwardsController::class);

        Orion::resource('calendars', CalendarsController::class);
        Orion::hasManyResource('calendars', 'events', CalendarsEventsController::class);

        Orion::resource('events', EventsController::class);

        Orion::resource('forms', FormsController::class);
        Orion::hasManyResource('forms', 'submissions', FormsSubmissionsController::class);

        Orion::resource('qualifications', QualificationsController::class);

        Orion::resource('ranks', RanksController::class);

        Orion::resource('roster', RosterController::class)->only('index');

        Orion::resource('submissions', SubmissionsController::class);

        Orion::resource('units', UnitsController::class);
        Orion::hasManyResource('units', 'users', UnitsUsersController::class);

        Orion::resource('users', UsersController::class);
        Orion::hasManyResource('users', 'assignment-records', UsersAssignmentRecordsController::class);
        Orion::hasManyResource('users', 'award-records', UsersAwardRecordsController::class);
        Orion::hasManyResource('users', 'combat-records', UsersCombatRecordsController::class);
        Orion::hasManyResource('users', 'qualification-records', UsersQualificationRecordsController::class);
        Orion::hasManyResource('users', 'rank-records', UsersRankRecordsController::class);
        Orion::hasManyResource('users', 'service-records', UsersServiceRecordsController::class);
        Orion::belongsToResource('users', 'position', UsersPositionController::class);
        Orion::belongsToResource('users', 'rank', UsersRankController::class);
        Orion::belongsToResource('users', 'specialty', UsersSpecialtyController::class);
        Orion::belongsToResource('users', 'unit', UsersUnitController::class);
        Orion::belongsToResource('users', 'status', UsersStatusController::class);
        Orion::belongsToManyResource('users', 'secondary-positions', UsersSecondaryPositionsController::class);
        Orion::belongsToManyResource('users', 'secondary-specialties', UsersSecondarySpecialtiesController::class);
        Orion::belongsToManyResource('users', 'secondary-units', UsersSecondaryUnitsController::class);
    });

    Route::fallback(static function () {
        throw new NotFoundHttpException('The requested API endpoint could not be found or you do not have access to it.');
    })->name('error');
});
