<?php

namespace App\Nova;

use App\Models\Scopes\VisibleScope;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class Group extends Resource
{
    use HasSortableRows;

    public static string $model = \App\Models\Group::class;

    public static array $orderBy = ['order' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        $query->withoutGlobalScope(VisibleScope::class);

        return $query;
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Name')
                ->sortable()
                ->rules('required'),
            Textarea::make('Description')
                ->nullable(),
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
            BelongsToMany::make('Units')
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
        return [];
    }
}
