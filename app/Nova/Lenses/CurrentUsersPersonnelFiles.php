<?php

namespace App\Nova\Lenses;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Panel;
use Perscom\ResourceCustomField\ResourceCustomField;

class CurrentUsersPersonnelFiles extends Lens
{
    /**
     * @return string
     */
    public function name()
    {
        return 'My Personnel Files';
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
            $request->withFilters(
                $query->whereHas('users', function (Builder $query) {
                    $query->where('user_id', '=', Auth::user()->getAuthIdentifier());
                })
            )
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
            ID::make()->sortable(),
            Text::make('First Name')->sortable(),
            Text::make('Last Name')->sortable(),
            Email::make('Email')->sortable(),
            Text::make('Status', function ($model) {
                return $model->status->name ?? null;
            }),
            Text::make('Rank', function ($model) {
                return $model->rank->name ?? null;
            }),
            Text::make('Specialty', function ($model) {
                return $model->assignment->specialty->name ?? null;
            }),
            Text::make('Position', function ($model) {
                return $model->assignment->position->name ?? null;
            }),
            Text::make('Unit', function ($model) {
                return $model->assignment->unit->name ?? null;
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
        return 'current-users-personnel-files';
    }
}
