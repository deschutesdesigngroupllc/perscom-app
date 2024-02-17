<?php

use App\Http\Controllers\Api\V1\Announcements\AnnouncementsController;
use App\Http\Controllers\Api\V1\AssignmentRecords\AssignmentRecordsController;
use App\Http\Controllers\Api\V1\AwardRecords\AwardRecordsController;
use App\Http\Controllers\Api\V1\Awards\AwardsController;
use App\Http\Controllers\Api\V1\Awards\AwardsImageController;
use App\Http\Controllers\Api\V1\Calendars\CalendarsController;
use App\Http\Controllers\Api\V1\Categories\CategoriesAwardsController;
use App\Http\Controllers\Api\V1\Categories\CategoriesController;
use App\Http\Controllers\Api\V1\Categories\CategoriesDocumentsController;
use App\Http\Controllers\Api\V1\Categories\CategoriesFormsController;
use App\Http\Controllers\Api\V1\Categories\CategoriesQualificationsController;
use App\Http\Controllers\Api\V1\Categories\CategoriesRanksController;
use App\Http\Controllers\Api\V1\CombatRecords\CombatRecordsController;
use App\Http\Controllers\Api\V1\Documents\DocumentsController;
use App\Http\Controllers\Api\V1\Events\EventsController;
use App\Http\Controllers\Api\V1\Events\EventsImagesController;
use App\Http\Controllers\Api\V1\Forms\FormsController;
use App\Http\Controllers\Api\V1\Forms\FormsSubmissionsController;
use App\Http\Controllers\Api\V1\Groups\GroupsController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\Newsfeed\NewsfeedController;
use App\Http\Controllers\Api\V1\Newsfeed\NewsfeedLikesController;
use App\Http\Controllers\Api\V1\Positions\PositionsController;
use App\Http\Controllers\Api\V1\QualificationRecords\QualificationRecordsController;
use App\Http\Controllers\Api\V1\Qualifications\QualificationsController;
use App\Http\Controllers\Api\V1\Qualifications\QualificationsImageController;
use App\Http\Controllers\Api\V1\RankRecords\RankRecordsController;
use App\Http\Controllers\Api\V1\Ranks\RanksController;
use App\Http\Controllers\Api\V1\Ranks\RanksImageController;
use App\Http\Controllers\Api\V1\ServiceRecords\ServiceRecordsController;
use App\Http\Controllers\Api\V1\SpecController;
use App\Http\Controllers\Api\V1\Specialties\SpecialtiesController;
use App\Http\Controllers\Api\V1\Statuses\StatusesController;
use App\Http\Controllers\Api\V1\Submissions\SubmissionsController;
use App\Http\Controllers\Api\V1\Submissions\SubmissionsStatusesController;
use App\Http\Controllers\Api\V1\Tasks\TasksController;
use App\Http\Controllers\Api\V1\Units\UnitsController;
use App\Http\Controllers\Api\V1\Users\UsersAssignmentRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersAwardRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersCombatRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersController;
use App\Http\Controllers\Api\V1\Users\UsersFieldsController;
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
use App\Http\Controllers\Api\V1\Users\UsersStatusRecordsController;
use App\Http\Controllers\Api\V1\Users\UsersTasksController;
use App\Http\Controllers\Api\V1\Users\UsersUnitController;
use App\Http\Middleware\InitializeTenancyByRequestData;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::group(['prefix' => 'v1'], static function () {
    Route::get('spec.yaml', [SpecController::class, 'index'])
        ->name('spec');

    Route::group([
        'middleware' => [
            'auth:api',
            InitializeTenancyByRequestData::class,
            PreventAccessFromCentralDomains::class,
            'subscribed',
            'approved',
        ],
    ], static function () {
        Orion::resource('me', MeController::class)
            ->only('index');

        Orion::resource('announcements', AnnouncementsController::class);

        Orion::resource('assignment-records', AssignmentRecordsController::class);

        Orion::resource('awards', AwardsController::class);
        Orion::hasOneResource('awards', 'image', AwardsImageController::class);

        Orion::resource('award-records', AwardRecordsController::class);

        Orion::resource('calendars', CalendarsController::class);

        Orion::resource('categories', CategoriesController::class);
        Orion::belongsToManyResource('categories', 'awards', CategoriesAwardsController::class);
        Orion::belongsToManyResource('categories', 'documents', CategoriesDocumentsController::class);
        Orion::belongsToManyResource('categories', 'forms', CategoriesFormsController::class);
        Orion::belongsToManyResource('categories', 'qualifications', CategoriesQualificationsController::class);
        Orion::belongsToManyResource('categories', 'ranks', CategoriesRanksController::class);

        Orion::resource('combat-records', CombatRecordsController::class);

        Orion::resource('documents', DocumentsController::class);

        Orion::resource('events', EventsController::class);
        Orion::hasManyResource('events', 'images', EventsImagesController::class);

        Orion::resource('forms', FormsController::class);
        Orion::hasManyResource('forms', 'submissions', FormsSubmissionsController::class);

        Orion::resource('groups', GroupsController::class);

        Orion::resource('newsfeed', NewsfeedController::class);
        Orion::morphToManyResource('newsfeed', 'likes', NewsfeedLikesController::class)
            ->only(['index', 'attach', 'detach', 'sync']);

        Orion::resource('positions', PositionsController::class);

        Orion::resource('qualifications', QualificationsController::class);
        Orion::hasOneResource('qualifications', 'image', QualificationsImageController::class);

        Orion::resource('qualification-records', QualificationRecordsController::class);

        Orion::resource('ranks', RanksController::class);
        Orion::hasOneResource('ranks', 'image', RanksImageController::class);

        Orion::resource('rank-records', RankRecordsController::class);

        Orion::resource('service-records', ServiceRecordsController::class);

        Orion::resource('specialties', SpecialtiesController::class);

        Orion::resource('statuses', StatusesController::class);

        Orion::resource('submissions', SubmissionsController::class);
        Orion::morphToManyResource('submissions', 'statuses', SubmissionsStatusesController::class);

        Orion::resource('tasks', TasksController::class);

        Orion::resource('units', UnitsController::class);

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
        Orion::belongsToManyResource('users', 'tasks', UsersTasksController::class);
        Orion::morphToManyResource('users', 'fields', UsersFieldsController::class);
        Orion::morphToManyResource('users', 'status-records', UsersStatusRecordsController::class);
    });

    Route::fallback(static function () {
        throw new NotFoundHttpException('The requested API endpoint could not be found or you do not have access to it.');
    })->name('error');
});
