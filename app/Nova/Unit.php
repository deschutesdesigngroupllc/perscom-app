<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Models\Scopes\VisibleScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;
use Outl1ne\NovaSortable\Traits\HasSortableManyToManyRows;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class Unit extends Resource
{
    use HasSortableManyToManyRows;
    use HasSortableRows;

    public static string $model = \App\Models\Unit::class;

    public static array $orderBy = ['order' => 'asc'];

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
        return Str::plural(Str::title(setting('localization_units', 'Units')));
    }

    public static function uriKey(): string
    {
        return Str::plural(Str::slug(setting('localization_units', 'units')));
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->withoutGlobalScope(VisibleScope::class);
    }

    public static function relatableQuery(NovaRequest $request, $query): Builder
    {
        return $query->withoutGlobalScope(VisibleScope::class);
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
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->onlyOnDetail(),
            DateTime::make('Updated At')
                ->onlyOnDetail(),
            new Panel('Roster', [
                Boolean::make('Hidden')
                    ->help('Hide this group from the roster.'),
            ]),
            BelongsToMany::make('Groups')
                ->showCreateRelationButton(),
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
