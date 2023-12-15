<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Models\Enums\RankRecordType;
use App\Nova\Actions\BatchCreateRankRecord;
use App\Nova\Metrics\NewRankRecords;
use App\Nova\Metrics\RankRecordsByType;
use App\Nova\Metrics\TotalRankRecords;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Perscom\DocumentViewerTool\DocumentViewerTool;

class RankRecord extends Resource
{
    public static string $model = \App\Models\RankRecord::class;

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
        return Str::singular(Str::slug(setting('localization_ranks', 'rank'))).'-records';
    }

    public static function label(): string
    {
        return Str::singular(Str::title(setting('localization_ranks', 'Rank'))).' Records';
    }

    public function title(): ?string
    {
        return "{$this->id} - {$this->user?->name} - {$this->rank?->name}";
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
            BelongsTo::make(Str::singular(Str::title(setting('localization_ranks', 'Rank'))), 'rank', \App\Nova\Rank::class)
                ->sortable()
                ->showCreateRelationButton(),
            Select::make('Type')
                ->options(collect(RankRecordType::cases())->mapWithKeys(fn (RankRecordType $case) => [$case->value => $case->getLabel()]))
                ->rules('required')
                ->displayUsingLabels(),
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
        return [new TotalRankRecords(), new NewRankRecords(), new RankRecordsByType()];
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
            (new BatchCreateRankRecord())->canSee(function () {
                return Gate::check('create', \App\Models\RankRecord::class);
            }),
            ExportAsCsv::make('Export '.self::label())
                ->canSee(function () {
                    return Feature::active(ExportDataFeature::class);
                })
                ->nameable(),
        ];
    }
}
