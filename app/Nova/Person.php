<?php

namespace App\Nova;

use App\Nova\Filters\PersonnelRank;
use App\Nova\Filters\PersonnelStatus;
use App\Nova\Lenses\CurrentUsersPersonnelFiles;
use App\Nova\Metrics\NewPersonnel;
use App\Nova\Metrics\TotalPersonnel;
use App\Nova\Records\Assignment as AssignmentRecords;
use App\Nova\Records\Award as AwardRecords;
use App\Nova\Records\Combat as CombatRecords;
use App\Nova\Records\Qualification as QualificationRecords;
use App\Nova\Records\Rank as RankRecords;
use App\Nova\Records\Service as ServiceRecords;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Perscom\ResourceCustomField\ResourceCustomField;

class Person extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Person::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'full_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'first_name', 'last_name'];

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'soldiers';
    }

    /**
     * @return string
     */
    public static function label()
    {
        return 'Soldiers';
    }

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
                Text::make('First Name')
                    ->sortable()
                    ->rules(['required'])
                    ->showOnPreview(),
                Text::make('Last Name')
                    ->sortable()
                    ->showOnPreview(),
                Email::make('Email')
                    ->sortable()
                    ->rules(['required', 'email', 'unique:people'])
                    ->hideFromIndex()
                    ->showOnPreview(),
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
                Heading::make('Meta')->onlyOnDetail(),
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
                //		        BelongsToMany::make('Rank History', 'ranks', Rank::class)->fields(function ($request, $relatedModel) {
                //			        return [
                //				        Text::make('Text'),
                //				        Select::make('Type')->options([
                //					        \App\Models\Records\Rank::RECORD_RANK_PROMOTION => 'Promotion',
                //					        \App\Models\Records\Rank::RECORD_RANK_DEMOTION => 'Demotion',
                //				        ]),
                //				        DateTime::make('Created At')->help('This field will be auto-generated if left blank.'),
                //				        DateTime::make('Updated At')->exceptOnForms()
                //			        ];
                //		        })
            ]),
            new Panel('Combat Assignment', [
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
            BelongsToMany::make('User Accounts', 'users', User::class),
            MorphToMany::make('Status History', 'statuses', Status::class)->fields(function () {
                return [
                    Textarea::make('Text'),
                    DateTime::make('Created At')
                        ->sortable()
                        ->onlyOnIndex(),
                ];
            }),
            new Panel('Important Dates', [
                //		        Text::make('Enlistment Date', function ($model) {
                //			        return Carbon::now()->diffForHumans($model->rank->record->created_at, CarbonInterface::DIFF_ABSOLUTE, false, 3);
                //		        })->onlyOnDetail(),
            ]),
            HasMany::make('Assignment Records', 'assignment_records', AssignmentRecords::class),
            HasMany::make('Award Records', 'award_records', AwardRecords::class),
            HasMany::make('Combat Records', 'combat_records', CombatRecords::class),
            HasMany::make('Rank Records', 'rank_records', RankRecords::class),
            HasMany::make('Service Records', 'service_records', ServiceRecords::class),
            HasMany::make('Qualification Records', 'qualification_records', QualificationRecords::class),
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
        return [
            (new TotalPersonnel())->width('1/2'),
            (new NewPersonnel())->width('1/2'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [new PersonnelStatus(), new PersonnelRank()];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [new CurrentUsersPersonnelFiles()];
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
