<?php

namespace App\Nova;

use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\TotalUsers;
use App\Nova\Metrics\UsersOnline;
use App\Nova\Records\Assignment as AssignmentRecords;
use App\Nova\Records\Award as AwardRecords;
use App\Nova\Records\Combat as CombatRecords;
use App\Nova\Records\Qualification as QualificationRecords;
use App\Nova\Records\Rank as RankRecords;
use App\Nova\Records\Service as ServiceRecords;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'name', 'email'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            new Panel('Demographics', [
                ID::make()->sortable(),
                Gravatar::make()->maxWidth(50),
                Text::make('Name')
                    ->sortable()
                    ->rules('required', 'max:255')
                    ->showOnPreview(),
                Text::make('Email')
                    ->sortable()
                    ->rules('required', 'email', 'max:254')
                    ->creationRules('unique:users,email')
                    ->updateRules('unique:users,email,{{resourceId}}')
                    ->showOnPreview(),
                Boolean::make('Email Verified', function () {
                    return $this->email_verified_at !== null;
                }),
                Password::make('Password')
                    ->onlyOnForms()
                    ->creationRules('required', Rules\Password::defaults())
                    ->updateRules('nullable', Rules\Password::defaults()),
                Text::make('Status', function ($model) {
                    return $model->status->name ?? null;
                })->showOnPreview(),
                Text::make('Rank', function ($model) {
                    return $model->rank->name ?? null;
                })
                    ->onlyOnIndex()
                    ->showOnPreview(),
                Text::make('Specialty', function ($model) {
                    return $model->assignment->specialty->name ?? null;
                })
                    ->onlyOnIndex()
                    ->showOnPreview(),
                Text::make('Position', function ($model) {
                    return $model->assignment->position->name ?? null;
                })
                    ->onlyOnIndex()
                    ->showOnPreview(),
                Text::make('Unit', function ($model) {
                    return $model->assignment->unit->name ?? null;
                })
                    ->onlyOnIndex()
                    ->showOnPreview(),
                Badge::make('Online', function ($user) {
                    return Cache::tags('user.online')->has("user.online.$user->id") ? 'online' : 'offline';
                })
                    ->map([
                        'offline' => 'info',
                        'online' => 'success',
                    ])
                    ->exceptOnForms(),
                Heading::make('Meta')->onlyOnDetail(),
                DateTime::make('Last Seen At')
                    ->displayUsing(function ($lastSeenAt) {
                        return optional($lastSeenAt, function () use ($lastSeenAt) {
                            return $lastSeenAt->longRelativeToNowDiffForHumans();
                        });
                    })
                    ->onlyOnDetail(),
                DateTime::make('Created At')->onlyOnDetail(),
                DateTime::make('Updated At')->onlyOnDetail(),
            ]),
            new Panel('Rank Information', [
                Text::make('Rank', function ($model) {
                    return $model->rank->name ?? null;
                })->onlyOnDetail(),
                DateTime::make('Last Promotion Date', function ($model) {
                    return $model->rank->record->created_at ?? null;
                })->onlyOnDetail(),
                Text::make('Time In Grade', function ($model) {
                    return $model->rank
                        ? Carbon::now()->diffForHumans(
                            $model->rank->record->created_at,
                            CarbonInterface::DIFF_ABSOLUTE,
                            false,
                            3
                        )
                        : null;
                })->onlyOnDetail(),
            ]),
            new Panel('Assignment', [
                Text::make('Position', function ($model) {
                    return $model->assignment->position->name ?? null;
                })->onlyOnDetail(),
                Text::make('Specailty', function ($model) {
                    return $model->assignment->specialty->name ?? null;
                })->onlyOnDetail(),
                Text::make('Unit', function ($model) {
                    return $model->assignment->unit->name ?? null;
                })->onlyOnDetail(),
            ]),
            MorphToMany::make('Status History', 'statuses', Status::class)->fields(function () {
                return [
                    Textarea::make('Text'),
                    DateTime::make('Created At')
                        ->sortable()
                        ->onlyOnIndex(),
                ];
            }),
            HasMany::make('Assignment Records', 'assignment_records', AssignmentRecords::class),
            HasMany::make('Award Records', 'award_records', AwardRecords::class),
            HasMany::make('Combat Records', 'combat_records', CombatRecords::class),
            HasMany::make('Rank Records', 'rank_records', RankRecords::class),
            HasMany::make('Service Records', 'service_records', ServiceRecords::class),
            HasMany::make('Qualification Records', 'qualification_records', QualificationRecords::class),
            new Panel('Permissions', [MorphedByMany::make('Roles'), MorphedByMany::make('Permissions')]),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [new TotalUsers(), new NewUsers(), new UsersOnline()];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
