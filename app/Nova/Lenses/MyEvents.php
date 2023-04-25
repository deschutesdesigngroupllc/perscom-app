<?php

namespace App\Nova\Lenses;

use App\Nova\User;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;

class MyEvents extends Lens
{
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->forUser($request->user())
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            BelongsTo::make('Event')->sortable(),
            BelongsTo::make('Calendar')->sortable(),
            BelongsTo::make('Organizer', 'organizer', User::class)->sortable(),
            Text::make('Description', function () {
                return Str::limit($this->event?->description);
            }),
            DateTime::make('Registered', 'created_at')->sortable(),
            Text::make('Starts', function () {
                return optional($this->event?->start, function ($start) {
                    return $this->event?->all_day ? $start->toFormattedDayDateString() : $start->toDayDateTimeString();
                });
            })->exceptOnForms(),
            Text::make('Ends', function () {
                return optional($this->event?->computed_end, function ($end) {
                    return $this->event?->all_day ? $end->toFormattedDayDateString() : $end->toDayDateTimeString();
                });
            })->exceptOnForms(),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'my-events';
    }
}
