<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Pennant\Feature;

class Status extends Resource
{
    public static string $model = \App\Models\Status::class;

    public static array $orderBy = ['name' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public static function label(): string
    {
        return Str::plural(Str::title(setting('localization_statuses', 'Statuses')));
    }

    public static function uriKey(): string
    {
        return Str::plural(Str::slug(setting('localization_statuses', 'statuses')));
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->rules(['required'])
                ->showOnPreview(),
            Color::make('Text Color')
                ->rules(['required']),
            Color::make('Background Color', 'bg_color')
                ->rules(['required']),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->onlyOnDetail(),
            DateTime::make('Updated At')
                ->onlyOnDetail(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
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
        return [ExportAsCsv::make('Export '.self::label())
            ->canSee(function () {
                return Feature::active(ExportDataFeature::class);
            })
            ->nameable()];
    }
}
