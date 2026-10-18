<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Http\Controllers\Api\Announcements\AnnouncementsController;
use App\Http\Controllers\Api\AssignmentRecords\AssignmentRecordsController;
use App\Http\Controllers\Api\AwardRecords\AwardRecordsController;
use App\Http\Controllers\Api\Awards\AwardsController;
use App\Http\Controllers\Api\Calendars\CalendarsController;
use App\Http\Controllers\Api\CombatRecords\CombatRecordsController;
use App\Http\Controllers\Api\Competencies\CompetenciesController;
use App\Http\Controllers\Api\Credentials\CredentialsController;
use App\Http\Controllers\Api\Documents\DocumentsController;
use App\Http\Controllers\Api\Events\EventsController;
use App\Http\Controllers\Api\Forms\FormsController;
use App\Http\Controllers\Api\Groups\GroupsController;
use App\Http\Controllers\Api\Issuers\IssuersController;
use App\Http\Controllers\Api\Positions\PositionsController;
use App\Http\Controllers\Api\QualificationRecords\QualificationRecordsController;
use App\Http\Controllers\Api\Qualifications\QualificationsController;
use App\Http\Controllers\Api\RankRecords\RankRecordsController;
use App\Http\Controllers\Api\Ranks\RanksController;
use App\Http\Controllers\Api\ServiceRecords\ServiceRecordsController;
use App\Http\Controllers\Api\Slots\SlotsController;
use App\Http\Controllers\Api\Specialties\SpecialtiesController;
use App\Http\Controllers\Api\Statuses\StatusesController;
use App\Http\Controllers\Api\Submissions\SubmissionsController;
use App\Http\Controllers\Api\Tasks\TasksController;
use App\Http\Controllers\Api\TrainingRecords\TrainingRecordsController;
use App\Http\Controllers\Api\Units\UnitsController;
use App\Http\Controllers\Api\Users\UsersController;
use Orion\Http\Controllers\Controller;
use ReflectionClass;

/**
 * The REST API resources exposed to MCP clients. Every MCP read is executed against these
 * endpoints, so the API's scopes, policies, validation and serialization stay the single
 * source of truth.
 */
class ApiResourceRegistry
{
    /**
     * @return array<string, array{endpoint: string, controller: class-string<Controller>, description: string, scope: string}>
     */
    public static function all(): array
    {
        return [
            'users' => static::entry('users', UsersController::class, 'Members of the organization, including their rank, position, specialty, status and unit.', 'view:user'),
            'ranks' => static::entry('ranks', RanksController::class, 'The rank structure, ordered from most to least senior.', 'view:rank'),
            'units' => static::entry('units', UnitsController::class, 'Units that members are assigned to.', 'view:unit'),
            'groups' => static::entry('groups', GroupsController::class, 'Groups that organize units into a hierarchy.', 'view:group'),
            'positions' => static::entry('positions', PositionsController::class, 'Positions members can hold.', 'view:position'),
            'specialties' => static::entry('specialties', SpecialtiesController::class, 'Specialties members can be trained in.', 'view:specialty'),
            'statuses' => static::entry('statuses', StatusesController::class, 'Statuses such as Active, Inactive or On Leave.', 'view:status'),
            'slots' => static::entry('slots', SlotsController::class, 'Unit slots that combine a position and specialty.', 'view:slot'),
            'awards' => static::entry('awards', AwardsController::class, 'Awards and decorations that can be issued.', 'view:award'),
            'qualifications' => static::entry('qualifications', QualificationsController::class, 'Qualifications that can be earned.', 'view:qualification'),
            'credentials' => static::entry('credentials', CredentialsController::class, 'Credentials such as certifications and licenses.', 'view:credential'),
            'competencies' => static::entry('competencies', CompetenciesController::class, 'Competencies evaluated during training.', 'view:competency'),
            'issuers' => static::entry('issuers', IssuersController::class, 'Organizations that issue credentials.', 'view:issuer'),
            'rank-records' => static::entry('rank-records', RankRecordsController::class, 'Promotions and demotions.', 'view:rankrecord'),
            'assignment-records' => static::entry('assignment-records', AssignmentRecordsController::class, 'Unit, position and specialty assignments.', 'view:assignmentrecord'),
            'award-records' => static::entry('award-records', AwardRecordsController::class, 'Awards issued to members.', 'view:awardrecord'),
            'qualification-records' => static::entry('qualification-records', QualificationRecordsController::class, 'Qualifications earned by members.', 'view:qualificationrecord'),
            'service-records' => static::entry('service-records', ServiceRecordsController::class, 'General service history entries.', 'view:servicerecord'),
            'training-records' => static::entry('training-records', TrainingRecordsController::class, 'Training completed by members, including credentials and competencies.', 'view:trainingrecord'),
            'combat-records' => static::entry('combat-records', CombatRecordsController::class, 'Combat and operation history.', 'view:combatrecord'),
            'events' => static::entry('events', EventsController::class, 'Scheduled events such as drills and operations.', 'view:event'),
            'calendars' => static::entry('calendars', CalendarsController::class, 'Calendars that group events.', 'view:calendar'),
            'announcements' => static::entry('announcements', AnnouncementsController::class, 'Organization announcements.', 'view:announcement'),
            'forms' => static::entry('forms', FormsController::class, 'Custom forms members can submit.', 'view:form'),
            'submissions' => static::entry('submissions', SubmissionsController::class, 'Form submissions.', 'view:submission'),
            'documents' => static::entry('documents', DocumentsController::class, 'Documents such as SOPs and orders.', 'view:document'),
            'tasks' => static::entry('tasks', TasksController::class, 'Tasks that can be assigned to members.', 'view:task'),
        ];
    }

    /**
     * @return array{endpoint: string, controller: class-string<Controller>, description: string, scope: string}|null
     */
    public static function find(string $key): ?array
    {
        return static::all()[$key] ?? null;
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(static::all());
    }

    /**
     * The scopes MCP clients should request so the API grants read access to every exposed resource.
     *
     * @return array<int, string>
     */
    public static function scopes(): array
    {
        return collect(static::all())
            ->pluck('scope')
            ->prepend('mcp:use')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * The fields a resource can be filtered, sorted, searched and expanded by, read straight
     * from its Orion controller so they never drift from what the API accepts.
     *
     * @return array{filterable: array<int, string>, sortable: array<int, string>, searchable: array<int, string>, includes: array<int, string>}
     */
    public static function capabilities(string $key): array
    {
        $resource = static::find($key);

        if (blank($resource)) {
            return ['filterable' => [], 'sortable' => [], 'searchable' => [], 'includes' => []];
        }

        /** @var Controller $controller */
        $controller = new ReflectionClass($resource['controller'])->newInstanceWithoutConstructor();

        return [
            'filterable' => array_values($controller->filterableBy()),
            'sortable' => array_values($controller->sortableBy()),
            'searchable' => array_values($controller->searchableBy()),
            'includes' => array_values($controller->includes()),
        ];
    }

    /**
     * @param  class-string<Controller>  $controller
     * @return array{endpoint: string, controller: class-string<Controller>, description: string, scope: string}
     */
    protected static function entry(string $endpoint, string $controller, string $description, string $scope): array
    {
        return [
            'endpoint' => $endpoint,
            'controller' => $controller,
            'description' => $description,
            'scope' => $scope,
        ];
    }
}
