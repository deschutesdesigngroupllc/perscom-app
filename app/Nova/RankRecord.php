<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
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
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Perscom\DocumentViewerTool\DocumentViewerTool;

class RankRecord extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\RankRecord::class;

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
        return Str::singular(Str::slug(setting('localization_ranks', 'rank'))).'-records';
    }

    /**
     * @return string
     */
    public static function label()
    {
        return Str::singular(Str::title(setting('localization_ranks', 'Rank'))).' Records';
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->id.optional($this->user, static function ($user) {
            return " - $user->name";
        });
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
            BelongsTo::make(Str::singular(Str::title(setting('localization_users', 'User'))), 'user', User::class)
                ->sortable(),
            BelongsTo::make(Str::singular(Str::title(setting('localization_ranks', 'Rank'))), 'rank', \App\Nova\Rank::class)
                ->sortable()
                ->showCreateRelationButton(),
            Select::make('Type')->options([
                \App\Models\RankRecord::RECORD_RANK_PROMOTION => 'Promotion',
                \App\Models\RankRecord::RECORD_RANK_DEMOTION => 'Demotion',
            ])->rules('required')->displayUsingLabels(),
            Textarea::make('Text')->alwaysShow(),
            Text::make('Text', function ($model) {
                return $model->text;
            })->onlyOnIndex(),
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
        return [new TotalRankRecords(), new NewRankRecords(), new RankRecordsByType()];
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
            (new BatchCreateRankRecord())->canSee(function () {
                return Gate::check('create', \App\Models\RankRecord::class);
            }),
            ExportAsCsv::make('Export '.self::label())->canSee(function () {
                return Feature::active(ExportDataFeature::class);
            })->nameable(),
        ];
    }
}
