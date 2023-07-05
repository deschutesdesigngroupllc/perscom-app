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
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Perscom\DocumentViewerTool\DocumentViewerTool;

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
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'assignment-records';
    }

    /**
     * @return string
     */
    public static function label()
    {
        return 'Assignment Records';
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
            ID::make()->sortable(),
            BelongsTo::make(Str::singular(Str::title(setting('localization_users', 'User'))), 'user', User::class)->sortable(),
            Panel::make('Position', [
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_positions', 'Position'))), 'position', Position::class)->sortable()->showCreateRelationButton(),
                MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_positions', 'Positions'))), 'secondary_position_ids')->options(
                    \App\Models\Position::all()->mapWithKeys(fn ($position) => [$position->id => $position->name])
                )->hideFromIndex(),
            ]),
            Panel::make('Specialty', [
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'specialty', Specialty::class)->sortable()->showCreateRelationButton(),
                MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_specialties', 'Specialties'))), 'secondary_specialty_ids')->options(
                    \App\Models\Specialty::all()->mapWithKeys(fn ($speciality) => [$speciality->id => $speciality->name])
                )->hideFromIndex(),
            ]),
            Panel::make('Unit', [
                BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_units', 'Unit'))), 'unit', Unit::class)->sortable()->showCreateRelationButton(),
                MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_units', 'Units'))), 'secondary_unit_ids')->options(
                    \App\Models\Unit::all()->mapWithKeys(fn ($unit) => [$unit->id => $unit->name])
                )->hideFromIndex(),
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
