<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Nova\Actions\BatchCreateAssignmentRecord;
use App\Nova\Metrics\NewAssignmentRecords;
use App\Nova\Metrics\TotalAssignmentRecords;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Perscom\DocumentViewerTool\DocumentViewerTool;
use Perscom\MessageField\MessageField;

class AssignmentRecord extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\AssignmentRecord::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'text'];

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return Str::singular(Str::title(setting('localization_assignment', 'Assignment'))).' Records';
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::slug(Str::singular(setting('localization_assignment', 'assignment')).' records');
    }

    /**
     * @return string
     */
    public function title()
    {
        return "{$this->id} - {$this->user?->name} - {$this->unit?->name} - {$this->position?->name}";
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "Created At: {$this->created_at->toDayDateTimeString()}";
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            MessageField::make('For Your Information')
                ->text('When editing an assignment record, the updated assignment values will not be applied to the user\'s profile. The user\'s profile is only updated during newly created assignment records.')
                ->color('warning')
                ->onlyOnForms()
                ->hideWhenCreating(),
            MessageField::make('For Your Information')
                ->text('When creating an assignment record, all assignment values will be directly applied to the user\'s profile.')
                ->color('info')
                ->onlyOnForms()
                ->hideWhenUpdating(),
            ID::make()->sortable(),
            BelongsTo::make(Str::singular(Str::title(setting('localization_users', 'User'))), 'user', User::class)->sortable(),
            Panel::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), [
                Boolean::make('Change '.Str::singular(Str::title(setting('localization_statuses', 'Status'))), 'change_status')
                    ->help('Leave unchecked to keep the status as is.')
                    ->fillUsing(fn () => null)
                    ->onlyOnForms(),
                BelongsTo::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), 'status', Status::class)
                    ->help('Leave blank to remove the status.')
                    ->nullable()
                    ->sortable()
                    ->showCreateRelationButton()
                    ->hide()
                    ->dependsOn(['change_status'], static function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_status) {
                            $field->show();
                        }
                    }),
            ]),
            Panel::make(Str::singular(Str::title(setting('localization_positions', 'Position'))), [
                Boolean::make('Change '.Str::singular(Str::title(setting('localization_positions', 'Position'))), 'change_position')
                    ->help('Leave unchecked to keep the position as is.')
                    ->fillUsing(fn () => null)
                    ->onlyOnForms(),
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_positions', 'Position'))), 'position', Position::class)
                    ->help('Leave blank to remove the position.')
                    ->sortable()
                    ->nullable()
                    ->showCreateRelationButton()
                    ->hide()
                    ->dependsOn(['change_position'], static function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_position) {
                            $field->show();
                        }
                    }),
                MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_positions', 'Positions'))), 'secondary_position_ids')
                    ->help('Leave blank to remove the secondary positions.')
                    ->options(\App\Models\Position::all()->pluck('name', 'id')->sort())
                    ->hideFromIndex()
                    ->nullable()
                    ->hide()
                    ->dependsOn(['change_position'], static function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_position) {
                            $field->show();
                        }
                    }),
            ]),
            Panel::make(Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), [
                Boolean::make('Change '.Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'change_specialty')
                    ->help('Leave unchecked to keep the specialty as is.')
                    ->fillUsing(fn () => null)
                    ->onlyOnForms(),
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'specialty', Specialty::class)
                    ->help('Leave blank to remove the specialty.')
                    ->sortable()
                    ->nullable()
                    ->showCreateRelationButton()
                    ->hide()
                    ->dependsOn(['change_specialty'], static function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_specialty) {
                            $field->show();
                        }
                    }),
                MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_specialties', 'Specialties'))), 'secondary_specialty_ids')
                    ->help('Leave blank to remove the secondary specialties.')
                    ->options(\App\Models\Specialty::all()->pluck('name', 'id')->sort())
                    ->hideFromIndex()
                    ->nullable()
                    ->hide()
                    ->dependsOn(['change_specialty'], static function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_specialty) {
                            $field->show();
                        }
                    }),
            ]),
            Panel::make(Str::singular(Str::title(setting('localization_units', 'Unit'))), [
                Boolean::make('Change '.Str::singular(Str::title(setting('localization_units', 'Unit'))), 'change_unit')
                    ->help('Leave unchecked to keep the unit as is.')
                    ->fillUsing(fn () => null)
                    ->onlyOnForms(),
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_units', 'Unit'))), 'unit', Unit::class)
                    ->help('Leave blank to remove the unit.')
                    ->sortable()
                    ->nullable()
                    ->showCreateRelationButton()
                    ->hide()
                    ->dependsOn(['change_unit'], static function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_unit) {
                            $field->show();
                        }
                    }),
                MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_units', 'Units'))), 'secondary_unit_ids')
                    ->help('Leave blank to remove the secondary units.')
                    ->options(\App\Models\Unit::all()->pluck('name', 'id')->sort())
                    ->hideFromIndex()
                    ->nullable()
                    ->hide()
                    ->dependsOn(['change_unit'], static function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_unit) {
                            $field->show();
                        }
                    }),
            ]),
            Textarea::make('Text')->alwaysShow()->hideFromIndex(),
            BelongsTo::make('Document')->nullable()->onlyOnForms(),
            new Panel('History', [
                BelongsTo::make('Author', 'author', User::class)->onlyOnDetail(),
                DateTime::make('Created At')->sortable()->exceptOnForms(),
                DateTime::make('Updated At')->exceptOnForms()->hideFromIndex(),
            ]),
            (new DocumentViewerTool())->withTitle($this->document->name ?? null)->withContent($this->document?->toHtml($this->user, $this)),
            MorphMany::make('Attachments', 'attachments', Attachment::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [new TotalAssignmentRecords(), new NewAssignmentRecords()];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
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
        return [
            (new BatchCreateAssignmentRecord())->canSee(function () {
                return Gate::check('create', \App\Models\AssignmentRecord::class);
            }),
            ExportAsCsv::make('Export '.self::label())->canSee(function () {
                return Feature::active(ExportDataFeature::class);
            })->nameable(),
        ];
    }
}
