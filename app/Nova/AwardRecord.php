<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Nova\Actions\BatchCreateAwardRecord;
use App\Nova\Metrics\NewAwardRecords;
use App\Nova\Metrics\TotalAwardRecords;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Perscom\DocumentViewerTool\DocumentViewerTool;

class AwardRecord extends Resource
{
    public static string $model = \App\Models\AwardRecord::class;

    /**
     * @var string
     */
    public static $title = 'id';

    /**
     * @var array
     */
    public static $search = ['id', 'text'];

    public static function uriKey(): string
    {
        return Str::singular(Str::slug(setting('localization_awards', 'award'))).'-records';
    }

    public static function label(): string
    {
        return Str::singular(Str::title(setting('localization_awards', 'Award'))).' Records';
    }

    public function title(): ?string
    {
        return "{$this->id} - {$this->user?->name} - {$this->award?->name}";
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
            BelongsTo::make(Str::singular(Str::title(setting('localization_awards', 'Award'))), 'award', \App\Nova\Award::class)
                ->sortable()
                ->showCreateRelationButton(),
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
        return [new TotalAwardRecords(), new NewAwardRecords()];
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
            (new BatchCreateAwardRecord())->canSee(function () {
                return Gate::check('create', \App\Models\AwardRecord::class);
            }),
            ExportAsCsv::make('Export '.self::label())
                ->canSee(function () {
                    return Feature::active(ExportDataFeature::class);
                })
                ->nameable(),
        ];
    }
}
