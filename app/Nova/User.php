<?php

namespace App\Nova;

use App\Nova\Forms\Submission;
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
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    use HasTabs;
    use HasActionsInTabs;

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
     * @var string[]
     */
    public static $orderBy = ['name' => 'asc'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255')
                ->showOnPreview()
                ->readonly(function () {
                    return Request::isDemoMode();
                }),
            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->showOnPreview()
                ->readonly(function () {
                    return Request::isDemoMode();
                }),
            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults())
                ->readonly(function () {
                    return Request::isDemoMode();
                }),
            Badge::make('Status', function () {
                return $this->status->name ?? 'none';
            })
                ->types([
                    'none' => 'bg-gray-100 text-gray-600',
                    $this->status?->name => $this->status?->color,
                ])
                ->label(function () {
                    return $this->status->name ?? 'No Current Status';
                }),
            Badge::make('Online', function ($user) {
                return $user->online_status;
            })
                ->map([
                    'offline' => 'info',
                    'online' => 'success',
                ])
                ->exceptOnForms(),
            Tabs::make('Personnel File', [
                Tab::make('Demographics', [
                    Gravatar::make()->maxWidth(50),
                    Boolean::make('Email Verified', function () {
                        return $this->email_verified_at !== null;
                    }),
                    Stack::make('Rank', [
                        Line::make('Rank', function ($model) {
                            return $model->rank->name ?? null;
                        })->asSubTitle(),
                        Line::make('Last Rank Change Date', function ($model) {
                            return optional($model->rank?->record?->created_at, function ($date) {
                                return 'Updated: ' . Carbon::parse($date)->longRelativeToNowDiffForHumans();
                            });
                        })->asSmall(),
                    ])
                        ->onlyOnIndex()
                        ->showOnPreview(),
                    Stack::make('Specialty', [
                        Line::make('Specialty', function ($model) {
                            return $model->assignment->specialty->name ?? null;
                        })->asSubTitle(),
                        Line::make('Last Assignment Date', function ($model) {
                            return optional($model->assignment?->created_at, function ($date) {
                                return 'Updated: ' . Carbon::parse($date)->longRelativeToNowDiffForHumans();
                            });
                        })->asSmall(),
                    ])
                        ->onlyOnIndex()
                        ->showOnPreview(),
                    Stack::make('Position', [
                        Line::make('Position', function ($model) {
                            return $model->assignment->position->name ?? null;
                        })->asSubTitle(),
                        Line::make('Last Assignment Date', function ($model) {
                            return optional($model->assignment?->created_at, function ($date) {
                                return 'Updated: ' . Carbon::parse($date)->longRelativeToNowDiffForHumans();
                            });
                        })->asSmall(),
                    ])
                        ->onlyOnIndex()
                        ->showOnPreview(),
                    Stack::make('Unit', [
                        Line::make('Unit', function ($model) {
                            return $model->assignment->unit->name ?? null;
                        })->asSubTitle(),
                        Line::make('Last Assignment Date', function ($model) {
                            return optional($model->assignment?->created_at, function ($date) {
                                return 'Updated: ' . Carbon::parse($date)->longRelativeToNowDiffForHumans();
                            });
                        })->asSmall(),
                    ])
                        ->onlyOnIndex()
                        ->showOnPreview(),
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
                Tab::make('Assignment', [
                    Text::make('Position', function ($model) {
                        return $model->assignment->position->name ?? null;
                    })->onlyOnDetail(),
                    Text::make('Specailty', function ($model) {
                        return $model->assignment->specialty->name ?? null;
                    })->onlyOnDetail(),
                    Text::make('Unit', function ($model) {
                        return $model->assignment->unit->name ?? null;
                    })->onlyOnDetail(),
                    DateTime::make('Last Assignment Change Date', function ($model) {
                        return $model->assignment->created_at ?? null;
                    })->onlyOnDetail(),
                    Text::make('Time In Assignment', function ($model) {
                        return $model->assignment
                            ? Carbon::now()->diffForHumans(
                                $model->assignment->created_at,
                                CarbonInterface::DIFF_ABSOLUTE,
                                false,
                                3
                            )
                            : null;
                    })->onlyOnDetail(),
                ]),
                Tab::make('Rank', [
                    Text::make('Rank', function ($model) {
                        return $model->rank->name ?? null;
                    })->onlyOnDetail(),
                    DateTime::make('Last Rank Change Date', function ($model) {
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
                Tab::make('Logs', [$this->actionfield()]),
            ])->showTitle(true),
            Tabs::make('Records', [
                HasMany::make('Assignment Records', 'assignment_records', AssignmentRecords::class),
                HasMany::make('Award Records', 'award_records', AwardRecords::class),
                HasMany::make('Combat Records', 'combat_records', CombatRecords::class),
                HasMany::make('Rank Records', 'rank_records', RankRecords::class),
                HasMany::make('Service Records', 'service_records', ServiceRecords::class),
                MorphToMany::make('Status Records', 'statuses', Status::class)->fields(function () {
                    return [
                        Textarea::make('Text'),
                        Text::make('Text', function ($model) {
                            return $model->text;
                        }),
                        DateTime::make('Created At')
                            ->sortable()
                            ->onlyOnIndex(),
                    ];
                }),
                HasMany::make('Submission Records', 'submissions', Submission::class),
                HasMany::make('Qualification Records', 'qualification_records', QualificationRecords::class),
            ])->showTitle(true),
            Tabs::make('Permissions', [MorphedByMany::make('Roles'), MorphedByMany::make('Permissions')])->showTitle(
                true
            ),
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
        return [ExportAsCsv::make('Export Users')->nameable()];
    }
}
