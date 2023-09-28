<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Nova\Filters\Role;
use App\Nova\Filters\Status as StatusFilter;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\TotalUsers;
use App\Nova\Metrics\UsersOnline;
use App\Traits\HasFields;
use Carbon\CarbonInterval;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;

class User extends Resource
{
    use HasActionsInTabs;
    use HasFields;
    use HasTabs;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the
     * resource when being displayed.
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
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return Str::plural(Str::title(setting('localization_users', 'Users')));
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::plural(Str::slug(setting('localization_users', 'users')));
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->hideFromIndex(),
            Text::make('Name')->sortable()->rules('required', 'max:255')->showOnPreview()->readonly(function () {
                return Request::isDemoMode();
            }),
            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->showOnPreview()
                ->copyable()
                ->readonly(function () {
                    return Request::isDemoMode();
                }),
            Boolean::make('Email Verified', function () {
                return $this->email_verified_at !== null;
            })->onlyOnDetail(),
            Boolean::make('Approved')->sortable()->canSee(function (Request $request) {
                return $request->user()->hasRole('Admin') && setting('registration_admin_approval_required', false);
            }),
            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults())
                ->readonly(function () {
                    return Request::isDemoMode();
                }),
            Badge::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), function () {
                return $this->status->name ?? 'none';
            })->types([
                'none' => 'bg-gray-100 text-gray-600',
                $this->status?->name => $this->status?->color,
            ])->label(function () {
                return $this->status->name ?? 'No Current Status';
            })->showOnPreview(),
            Badge::make('Online', function ($user) {
                return $user->online;
            })->map([
                false => 'info',
                true => 'success',
            ])->showOnPreview()->exceptOnForms(),
            DateTime::make('Last Seen At')->displayUsing(function ($lastSeenAt) {
                return optional($lastSeenAt, function () use ($lastSeenAt) {
                    return $lastSeenAt->longRelativeToNowDiffForHumans();
                });
            })->onlyOnDetail(),
            Avatar::make('Profile Photo')->disk('s3_public')->deletable()->prunable()->squared()->hideFromIndex(),
            Image::make('Cover Photo')->disk('s3_public')->deletable()->prunable()->squared()->hideFromIndex(),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->onlyOnDetail(),
            DateTime::make('Updated At')->onlyOnDetail(),
            Tabs::make(Str::singular(Str::title(setting('localization_assignment', 'Assignment'))), [
                Tab::make('Current '.Str::singular(Str::title(setting('localization_assignment', 'Assignment'))), [
                    Stack::make('Primary '.Str::singular(Str::title(setting('localization_positions', 'Position'))), [
                        Line::make('Position', function ($model) {
                            return $model->position->name ?? null;
                        })->asSubTitle(),
                        Line::make('Last '.Str::singular(Str::title(setting('localization_assignment', 'Assignment'))).' Date', function ($model) {
                            return optional($model->assignment_records->first()?->created_at, function ($date) {
                                return 'Updated: '.Carbon::parse($date)->longRelativeToNowDiffForHumans();
                            });
                        })->asSmall(),
                    ])->showOnPreview(),
                    Stack::make('Primary '.Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), [
                        Line::make('Specialty', function ($model) {
                            return $model->specialty->name ?? null;
                        })->asSubTitle(),
                        Line::make('Last '.Str::singular(Str::title(setting('localization_assignment', 'Assignment'))).' Date', function ($model) {
                            return optional($model->assignment_records->first()?->created_at, function ($date) {
                                return 'Updated: '.Carbon::parse($date)->longRelativeToNowDiffForHumans();
                            });
                        })->asSmall(),
                    ])->showOnPreview(),
                    Stack::make('Primary '.Str::singular(Str::title(setting('localization_units', 'Unit'))), [
                        Line::make('Unit', function ($model) {
                            return $model->unit->name ?? null;
                        })->asSubTitle(),
                        Line::make('Last '.Str::singular(Str::title(setting('localization_assignment', 'Assignment'))).' Date', function ($model) {
                            return optional($model->assignment_records->first()?->created_at, function ($date) {
                                return 'Updated: '.Carbon::parse($date)->longRelativeToNowDiffForHumans();
                            });
                        })->asSmall(),
                    ])->showOnPreview(),
                    DateTime::make('Last '.Str::singular(Str::title(setting('localization_assignment', 'Assignment'))).' Change Date', function ($model) {
                        return $model->assignment_records->first()->created_at ?? null;
                    })->onlyOnDetail(),
                    Text::make('Time In '.Str::singular(Str::title(setting('localization_assignment', 'Assignment'))), function ($model) {
                        return optional($model->time_in_assignment, function ($date) {
                            return CarbonInterval::make($date)->forHumans();
                        });
                    })->onlyOnDetail(),
                ]),
                BelongsToMany::make('Secondary '.Str::plural(Str::title(setting('localization_positions', 'Positions'))), 'secondary_positions', Position::class)
                    ->showCreateRelationButton(),
                BelongsToMany::make('Secondary '.Str::plural(Str::title(setting('localization_specialties', 'Specialties'))), 'secondary_specialties', Specialty::class)
                    ->showCreateRelationButton(),
                BelongsToMany::make('Secondary '.Str::plural(Str::title(setting('localization_units', 'Units'))), 'secondary_units', Unit::class)
                    ->showCreateRelationButton(),
            ])->showTitle(),
            Panel::make(Str::singular(Str::title(setting('localization_assignment', 'Assignment'))), [
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_positions', 'Position'))), 'position', Position::class)
                    ->help('You can manually set the user\'s position. Creating an assignment record will also change their position.')
                    ->nullable()
                    ->onlyOnForms()
                    ->showOnPreview()
                    ->canSeeWhen('create', \App\Models\AssignmentRecord::class),
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'specialty', Specialty::class)
                    ->help('You can manually set the user\'s specialty. Creating an assignment record will also change their specialty.')
                    ->nullable()
                    ->onlyOnForms()
                    ->showOnPreview()
                    ->canSeeWhen('create', \App\Models\AssignmentRecord::class),
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_units', 'Unit'))), 'unit', Unit::class)
                    ->help('You can manually set the user\'.s unit. Creating an assignment record will also change their unit.')
                    ->nullable()
                    ->onlyOnForms()
                    ->showOnPreview()
                    ->canSeeWhen('create', \App\Models\AssignmentRecord::class),
            ]),
            $this->getNovaFields($request, true, 'Custom Fields'),
            BelongsToMany::make('Events')->referToPivotAs('registration'),
            MorphToMany::make('Fields', 'fields', Field::class)
                ->showCreateRelationButton(),
            new Panel('Notes', [
                Trix::make('Notes')->alwaysShow()->canSeeWhen('note', \App\Models\User::class),
                DateTime::make('Notes Last Updated At', 'notes_updated_at')
                    ->canSeeWhen('note', \App\Models\User::class)
                    ->onlyOnDetail(),
            ]),
            new Panel('Rank', [
                Stack::make(Str::singular(Str::title(setting('localization_ranks', 'Rank'))), [
                    Line::make('Rank', function ($model) {
                        return $model->rank->name ?? null;
                    })->asSubTitle(),
                    Line::make('Last '.Str::singular(Str::title(setting('localization_ranks', 'Rank'))).' Change Date', function ($model) {
                        return optional($model->rank_records->first()?->created_at, function ($date) {
                            return 'Updated: '.Carbon::parse($date)->longRelativeToNowDiffForHumans();
                        });
                    })->asSmall(),
                ])->showOnPreview(),
                DateTime::make('Last '.Str::singular(Str::title(setting('localization_ranks', 'Rank'))).' Change Date', function ($model) {
                    return $model->rank_records->first()->created_at ?? null;
                })->onlyOnDetail(),
                Text::make('Time In Grade', function ($model) {
                    return optional($model->time_in_grade, function ($date) {
                        return CarbonInterval::make($date)->forHumans();
                    });
                })->onlyOnDetail(),
            ]),
            Panel::make(Str::singular(Str::title(setting('localization_ranks', 'Rank'))), [
                BelongsTo::make(Str::singular(Str::title(setting('localization_ranks', 'Rank'))), 'rank', Rank::class)
                    ->help('You can manually set the user\'s rank. Creating a rank record will also change their rank.')
                    ->nullable()
                    ->onlyOnForms()
                    ->showOnPreview()
                    ->canSeeWhen('create', \App\Models\RankRecord::class),
            ]),
            Tabs::make('Records', [
                HasMany::make(Str::singular(Str::title(setting('localization_assignment', 'Assignment'))).' Records', 'assignment_records', AssignmentRecord::class)
                    ->canSee(function () {
                        return Gate::check('create', \App\Models\AssignmentRecord::class) || $this->resource->id === Auth::user()->getAuthIdentifier();
                    }),
                HasMany::make(Str::singular(Str::title(setting('localization_awards', 'Award'))).' Records', 'award_records', AwardRecord::class)
                    ->canSee(function () {
                        return Gate::check('create', \App\Models\AwardRecord::class) || $this->resource->id === Auth::user()->getAuthIdentifier();
                    }),
                HasMany::make(Str::singular(Str::title(setting('localization_combat', 'Combat'))).' Records', 'combat_records', CombatRecord::class)
                    ->canSee(function () {
                        return Gate::check('create', \App\Models\CombatRecord::class) || $this->resource->id === Auth::user()->getAuthIdentifier();
                    }),
                HasMany::make(Str::singular(Str::title(setting('localization_qualifications', 'Qualification'))).' Records', 'qualification_records', QualificationRecord::class)
                    ->canSee(function () {
                        return Gate::check('create', \App\Models\QualificationRecord::class) || $this->resource->id === Auth::user()->getAuthIdentifier();
                    }),
                HasMany::make(Str::singular(Str::title(setting('localization_ranks', 'Rank'))).' Records', 'rank_records', RankRecord::class)
                    ->canSee(function () {
                        return Gate::check('create', \App\Models\RankRecord::class) || $this->resource->id === Auth::user()->getAuthIdentifier();
                    }),
                HasMany::make(Str::singular(Str::title(setting('localization_service', 'Service'))).' Records', 'service_records', ServiceRecord::class)
                    ->canSee(function () {
                        return Gate::check('create', \App\Models\ServiceRecord::class) || $this->resource->id === Auth::user()->getAuthIdentifier();
                    }),
                HasMany::make('Submission Records', 'submissions', Submission::class),
            ])->showTitle(),
            MorphToMany::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), 'statuses', Status::class)
                ->allowDuplicateRelations()
                ->showCreateRelationButton()
                ->fields(function () {
                    return [
                        Textarea::make('Text'),
                        Text::make('Text', function ($model) {
                            return $model->text;
                        }),
                        DateTime::make('Created At')->sortable()->onlyOnIndex(),
                    ];
                }),
            Panel::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), [
                BelongsTo::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), 'status', Status::class)
                    ->help('You can manually set the user\'s status. Creating a status record will also change their status.')
                    ->nullable()
                    ->onlyOnForms()
                    ->canSeeWhen('create', \App\Models\StatusRecord::class),
            ]),
            Tabs::make('Settings', [
                new Panel('Logs', [$this->actionfield()]),
                MorphedByMany::make('Permissions'),
                MorphedByMany::make('Roles'),
            ])->showTitle(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [new TotalUsers(), new NewUsers(), new UsersOnline()];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [new Role(), new StatusFilter()];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [ExportAsCsv::make('Export Users')->canSee(function () {
            return Feature::active(ExportDataFeature::class);
        })->nameable()];
    }
}
