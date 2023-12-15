<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Nova\Actions\BatchCreateCombatRecord;
use App\Nova\Metrics\NewCombatRecords;
use App\Nova\Metrics\TotalCombatRecords;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Perscom\DocumentViewerTool\DocumentViewerTool;

class CombatRecord extends Resource
{
    public static string $model = \App\Models\CombatRecord::class;

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
        return Str::singular(Str::title(setting('localization_combat', 'Combat'))).' Records';
    }

    public static function uriKey(): string
    {
        return Str::slug(Str::singular(setting('localization_combat', 'combat')).' records');
    }

    public function title(): ?string
    {
        return $this->id.optional($this->user, static function ($user) {
            return " - $user->name";
        });
    }

    public function subtitle(): ?string
    {
        return "Created At: {$this->created_at->toDayDateTimeString()}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make(Str::singular(Str::title(setting('localization_users', 'User'))), 'user', User::class)
                ->sortable(),
            Textarea::make('Text')
                ->rules(['required'])
                ->hideFromIndex()
                ->alwaysShow(),
            Text::make('Text', function ($model) {
                return $model->text;
            })
                ->onlyOnIndex(),
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
        return [new TotalCombatRecords(), new NewCombatRecords()];
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
            (new BatchCreateCombatRecord())->canSee(function () {
                return Gate::check('create', \App\Models\CombatRecord::class);
            }),
            ExportAsCsv::make('Export '.self::label())
                ->canSee(function () {
                    return Feature::active(ExportDataFeature::class);
                })
                ->nameable(),
        ];
    }
}
