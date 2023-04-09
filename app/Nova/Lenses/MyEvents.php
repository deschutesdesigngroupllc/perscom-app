<?php

namespace App\Nova\Lenses;

use App\Nova\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
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
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->forUser(Auth::user())
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
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
            Text::make('Starts', function () {
                return optional($this->event?->start, function ($start) {
                    return $this->event?->all_day ? $start->toFormattedDayDateString() : $start->toDayDateTimeString();
                });
            })->exceptOnForms(),
            Text::make('Ends', function () {
                return optional($this->event?->end, function ($end) {
                    return $this->event?->all_day ? $end->toFormattedDayDateString() : $end->toDayDateTimeString();
                });
            })->exceptOnForms(),
            Boolean::make('All Day', function () {
                return $this->event?->all_day;
            }),
            Boolean::make('Repeats', function () {
                return $this->event?->repeats;
            }),
            Boolean::make('Has Passed', function () {
                return $this->event?->is_past;
            }),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
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
