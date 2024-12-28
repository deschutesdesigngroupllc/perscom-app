<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Announcements\AnnouncementsController;
use App\Http\Controllers\Api\AssignmentRecords\AssignmentRecordsController;
use App\Http\Controllers\Api\Attachments\AttachmentsController;
use App\Http\Controllers\Api\AwardRecords\AwardRecordsController;
use App\Http\Controllers\Api\Awards\AwardsController;
use App\Http\Controllers\Api\Awards\AwardsImageController;
use App\Http\Controllers\Api\CacheController;
use App\Http\Controllers\Api\Calendars\CalendarsController;
use App\Http\Controllers\Api\Categories\CategoriesAwardsController;
use App\Http\Controllers\Api\Categories\CategoriesController;
use App\Http\Controllers\Api\Categories\CategoriesDocumentsController;
use App\Http\Controllers\Api\Categories\CategoriesFormsController;
use App\Http\Controllers\Api\Categories\CategoriesQualificationsController;
use App\Http\Controllers\Api\Categories\CategoriesRanksController;
use App\Http\Controllers\Api\CombatRecords\CombatRecordsController;
use App\Http\Controllers\Api\Comments\CommentsController;
use App\Http\Controllers\Api\Documents\DocumentsController;
use App\Http\Controllers\Api\Events\EventsController;
use App\Http\Controllers\Api\Events\EventsImageController;
use App\Http\Controllers\Api\Forms\FormsController;
use App\Http\Controllers\Api\Forms\FormsSubmissionsController;
use App\Http\Controllers\Api\Groups\GroupsController;
use App\Http\Controllers\Api\Groups\GroupsImageController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\Images\ImagesController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\Messages\MessagesController;
use App\Http\Controllers\Api\Newsfeed\NewsfeedController;
use App\Http\Controllers\Api\Newsfeed\NewsfeedLikesController;
use App\Http\Controllers\Api\Positions\PositionsController;
use App\Http\Controllers\Api\QualificationRecords\QualificationRecordsController;
use App\Http\Controllers\Api\Qualifications\QualificationsController;
use App\Http\Controllers\Api\Qualifications\QualificationsImageController;
use App\Http\Controllers\Api\RankRecords\RankRecordsController;
use App\Http\Controllers\Api\Ranks\RanksController;
use App\Http\Controllers\Api\Ranks\RanksImageController;
use App\Http\Controllers\Api\RosterController;
use App\Http\Controllers\Api\ServiceRecords\ServiceRecordsController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SpecController;
use App\Http\Controllers\Api\Specialties\SpecialtiesController;
use App\Http\Controllers\Api\Statuses\StatusesController;
use App\Http\Controllers\Api\Submissions\SubmissionsController;
use App\Http\Controllers\Api\Submissions\SubmissionsStatusesController;
use App\Http\Controllers\Api\Tasks\TasksController;
use App\Http\Controllers\Api\Units\UnitsController;
use App\Http\Controllers\Api\Units\UnitsImageController;
use App\Http\Controllers\Api\Users\UsersAssignmentRecordsController;
use App\Http\Controllers\Api\Users\UsersAttachmentsController;
use App\Http\Controllers\Api\Users\UsersAwardRecordsController;
use App\Http\Controllers\Api\Users\UsersCombatRecordsController;
use App\Http\Controllers\Api\Users\UsersController;
use App\Http\Controllers\Api\Users\UsersFieldsController;
use App\Http\Controllers\Api\Users\UsersPositionController;
use App\Http\Controllers\Api\Users\UsersQualificationRecordsController;
use App\Http\Controllers\Api\Users\UsersRankController;
use App\Http\Controllers\Api\Users\UsersRankRecordsController;
use App\Http\Controllers\Api\Users\UsersServiceRecordsController;
use App\Http\Controllers\Api\Users\UsersSpecialtyController;
use App\Http\Controllers\Api\Users\UsersStatusController;
use App\Http\Controllers\Api\Users\UsersStatusRecordsController;
use App\Http\Controllers\Api\Users\UsersTasksController;
use App\Http\Controllers\Api\Users\UsersUnitController;
use App\Http\Middleware\ApiHeaders;
use App\Http\Middleware\CheckApiVersion;
use App\Http\Middleware\InitializeTenancyByRequestData;
use App\Http\Middleware\LogApiRequest;
use App\Http\Middleware\LogApiResponse;
use App\Http\Middleware\SentryContext;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::get('health', HealthController::class)
    ->name('health');

Route::get('spec.json', [SpecController::class, 'index'])
    ->name('spec');

Route::group([
    'middleware' => [
        'auth_api',
        InitializeTenancyByRequestData::class,
        PreventAccessFromCentralDomains::class,
        CheckApiVersion::class,
        ApiHeaders::class,
        LogApiRequest::class,
        LogApiResponse::class,
        SentryContext::class,
        'subscribed',
        'approved',
    ],
    'prefix' => '{version}',
], static function () {
    Orion::resource('me', MeController::class)
        ->only('index');

    Orion::resource('announcements', AnnouncementsController::class);

    Orion::resource('assignment-records', AssignmentRecordsController::class);

    Orion::resource('attachments', AttachmentsController::class);

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

    Route::post('cache', CacheController::class)
        ->name('cache');

    Orion::resource('combat-records', CombatRecordsController::class);

    Orion::resource('comments', CommentsController::class);

    Orion::resource('documents', DocumentsController::class);

    Orion::resource('events', EventsController::class);
    Orion::hasManyResource('events', 'image', EventsImageController::class);

    Orion::resource('forms', FormsController::class);
    Orion::hasManyResource('forms', 'submissions', FormsSubmissionsController::class);

    Orion::resource('groups', GroupsController::class);
    Orion::hasOneResource('groups', 'image', GroupsImageController::class);

    Orion::resource('images', ImagesController::class);

    Orion::resource('messages', MessagesController::class)
        ->middleware('throttle:sms');

    Orion::resource('newsfeed', NewsfeedController::class)
        ->only(['index']);
    Orion::morphToManyResource('newsfeed', 'likes', NewsfeedLikesController::class)
        ->only(['index', 'attach', 'detach', 'sync']);

    Orion::resource('positions', PositionsController::class);

    Orion::resource('qualifications', QualificationsController::class);
    Orion::hasOneResource('qualifications', 'image', QualificationsImageController::class);

    Orion::resource('qualification-records', QualificationRecordsController::class);

    Orion::resource('ranks', RanksController::class);
    Orion::hasOneResource('ranks', 'image', RanksImageController::class);

    Orion::resource('rank-records', RankRecordsController::class);

    Orion::resource('roster', RosterController::class)
        ->only(['index', 'show']);

    Orion::resource('service-records', ServiceRecordsController::class);

    Orion::resource('settings', SettingsController::class)
        ->only('index');

    Orion::resource('specialties', SpecialtiesController::class);

    Orion::resource('statuses', StatusesController::class);

    Orion::resource('submissions', SubmissionsController::class);
    Orion::morphToManyResource('submissions', 'statuses', SubmissionsStatusesController::class);

    Orion::resource('tasks', TasksController::class);

    Orion::resource('units', UnitsController::class);
    Orion::hasOneResource('units', 'image', UnitsImageController::class);

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
    Orion::morphToManyResource('users', 'attachments', UsersAttachmentsController::class);
    Orion::morphToManyResource('users', 'fields', UsersFieldsController::class);
    Orion::morphToManyResource('users', 'status-records', UsersStatusRecordsController::class);
    Orion::belongsToManyResource('users', 'tasks', UsersTasksController::class);
});

Route::fallback(static function () {
    throw new NotFoundHttpException('The requested API endpoint could not be found or you do not have access to it.');
})->name('error');
