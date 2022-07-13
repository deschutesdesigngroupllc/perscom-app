<?php

namespace App\Nova\Lenses;

use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;

class CurrentUsersSubmissions extends Lens
{
    /**
     * @return string
     */
    public function name()
    {
        return 'My Form Submissions';
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering(
            $request->withFilters($query->where('user_id', '=', Auth::user()->getAuthIdentifier()))
        );
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
            //	        BelongsTo::make('Form'),
            Badge::make('Status', function ($model) {
                return $this->status->name ?? null;
            })->map([
                $this->status->name ?? null => 'info',
            ]),
            DateTime::make('Created At')->sortable(),
            DateTime::make('Updated At')->sortable(),
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
        return 'current-users-submissions';
    }
}
