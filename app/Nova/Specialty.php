<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use Emilianotisato\NovaTinyMCE\NovaTinyMCE;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Pennant\Feature;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class Specialty extends Resource
{
    use HasSortableRows;

    public static string $model = \App\Models\Specialty::class;

    public static array $orderBy = ['order' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name', 'abbreviation'];

    public static function label(): string
    {
        return Str::plural(Str::title(setting('localization_specialties', 'Specialties')));
    }

    public static function uriKey(): string
    {
        return Str::plural(Str::slug(setting('localization_specialties', 'specialties')));
    }

    public function subtitle(): ?string
    {
        return "Abbreviation: {$this->abbreviation}";
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
            Text::make('Abbreviation')
                ->sortable()
                ->showOnPreview(),
            NovaTinyMCE::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
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
