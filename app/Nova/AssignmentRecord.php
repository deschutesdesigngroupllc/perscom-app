<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Models\Enums\AssignmentRecordType;
use App\Nova\Actions\BatchCreateAssignmentRecord;
use App\Nova\Metrics\NewAssignmentRecords;
use App\Nova\Metrics\TotalAssignmentRecords;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Perscom\DocumentViewerTool\DocumentViewerTool;
use Perscom\MessageField\MessageField;

class AssignmentRecord extends Resource
{
    public static string $model = \App\Models\AssignmentRecord::class;

    /**
     * @var string
     */
    public static $title = 'id';

    /**
     * @var array
     */
    public static $search = ['id', 'text'];

    public static function label(): string
    {
        return Str::singular(Str::title(setting('localization_assignment', 'Assignment'))).' Records';
    }

    public static function uriKey(): string
    {
        return Str::slug(Str::singular(setting('localization_assignment', 'assignment')).' records');
    }

    public function title(): ?string
    {
        return "{$this->id} - {$this->user?->name} - {$this->unit?->name} - {$this->position?->name}";
    }

    public function subtitle(): ?string
    {
        return "Created At: {$this->created_at->toDayDateTimeString()}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            MessageField::make('For Your Information')
                ->text('When editing an assignment record, either primary or secondary, the updated assignment values will not change the user\'s current assignment. The user\'s current assignment is only updated during newly created primary assignment records. More detailed information can be obtained within our documentation.')
                ->color('warning')
                ->onlyOnForms()
                ->hideWhenCreating(),
            MessageField::make('For Your Information')
                ->text('Only primary assignment records will change a user\'s current assignment when created. Secondary assignment records are used to define potential alternative assignments the user is engaged in. Both primary and secondary assignments will show up on the Roster. More detailed information can be obtained within our documentation.')
                ->color('info')
                ->onlyOnForms()
                ->hideWhenUpdating(),
            ID::make()
                ->sortable(),
            BelongsTo::make(Str::singular(Str::title(setting('localization_users', 'User'))), 'user', User::class)
                ->sortable(),
            Badge::make('Type', function (\App\Models\AssignmentRecord $record) {
                return $record->type->getLabel();
            })->map([
                'Primary' => 'success',
                'Secondary' => 'info',
            ])->exceptOnForms(),
            Select::make('Type')
                ->options(collect(AssignmentRecordType::cases())->mapWithKeys(fn (AssignmentRecordType $case) => [$case->value => $case->getLabel()]))
                ->rules('required')
                ->displayUsingLabels()
                ->default(AssignmentRecordType::PRIMARY->value)
                ->onlyOnForms(),
            Panel::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), [
                Boolean::make('Assign '.Str::singular(Str::title(setting('localization_statuses', 'Status'))), 'change_status')
                    ->help('Add a '.Str::singular(Str::lower(setting('localization_statuses', 'status'))).' to the record.')
                    ->fillUsing(fn () => true)
                    ->resolveUsing(fn () => isset($this->status))
                    ->onlyOnForms(),
                BelongsTo::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), 'status', Status::class)
                    ->help('Leave blank to remove the status. This only applies to primary assignments.')
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
                Boolean::make('Assign '.Str::singular(Str::title(setting('localization_positions', 'Position'))), 'change_position')
                    ->help('Add a '.Str::singular(Str::lower(setting('localization_positions', 'position'))).' to the record.')
                    ->fillUsing(fn () => null)
                    ->resolveUsing(fn () => isset($this->position))
                    ->onlyOnForms(),
                BelongsTo::make(Str::singular(Str::title(setting('localization_positions', 'Position'))), 'position', Position::class)
                    ->help('Leave blank to remove the position. This only applies to primary assignments.')
                    ->sortable()
                    ->nullable()
                    ->showCreateRelationButton()
                    ->hide()
                    ->dependsOn(['change_position'], static function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_position) {
                            $field->show();
                        }
                    }),
            ]),
            Panel::make(Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), [
                Boolean::make('Assign '.Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'change_specialty')
                    ->help('Add a '.Str::singular(Str::lower(setting('localization_specialties', 'specialty'))).' to the record.')
                    ->fillUsing(fn () => null)
                    ->resolveUsing(fn () => isset($this->specialty))
                    ->onlyOnForms(),
                BelongsTo::make(Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'specialty', Specialty::class)
                    ->help('Leave blank to remove the specialty. This only applies to primary assignments.')
                    ->sortable()
                    ->nullable()
                    ->showCreateRelationButton()
                    ->hide()
                    ->dependsOn(['change_specialty'], static function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_specialty) {
                            $field->show();
                        }
                    }),
            ]),
            Panel::make(Str::singular(Str::title(setting('localization_units', 'Unit'))), [
                Boolean::make('Assign '.Str::singular(Str::title(setting('localization_units', 'Unit'))), 'change_unit')
                    ->help('Add a '.Str::singular(Str::lower(setting('localization_units', 'unit'))).' to the record.')
                    ->fillUsing(fn () => null)
                    ->resolveUsing(fn () => isset($this->unit))
                    ->onlyOnForms(),
                BelongsTo::make(Str::singular(Str::title(setting('localization_units', 'Unit'))), 'unit', Unit::class)
                    ->help('Leave blank to remove the unit. This only applies to primary assignments.')
                    ->sortable()
                    ->nullable()
                    ->showCreateRelationButton()
                    ->hide()
                    ->dependsOn(['change_unit'], static function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                        if ($formData->change_unit) {
                            $field->show();
                        }
                    }),
            ]),
            Textarea::make('Text')
                ->alwaysShow()
                ->hideFromIndex(),
            BelongsTo::make('Document')
                ->nullable()
                ->onlyOnForms(),
            new Panel('History', [
                BelongsTo::make('Author', 'author', User::class)
                    ->onlyOnDetail(),
                DateTime::make('Created At')
                    ->sortable()
                    ->exceptOnForms(),
                DateTime::make('Updated At')
                    ->exceptOnForms()
                    ->hideFromIndex(),
            ]),
            (new DocumentViewerTool())->withTitle($this->document->name ?? null)
                ->withContent($this->document?->toHtml($this->user, $this)),
            MorphMany::make('Attachments', 'attachments', Attachment::class),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [new TotalAssignmentRecords(), new NewAssignmentRecords()];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            (new BatchCreateAssignmentRecord())->canSee(function () {
                return Gate::check('create', \App\Models\AssignmentRecord::class);
            }),
            ExportAsCsv::make('Export '.self::label())
                ->canSee(function () {
                    return Feature::active(ExportDataFeature::class);
                })
                ->nameable(),
        ];
    }
}
