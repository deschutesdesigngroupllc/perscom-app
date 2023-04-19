<?php

namespace App\Nova;

use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Domain extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Domain::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'domain'];

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return $this->url;
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "Tenant: {$this->tenant->name}";
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
            BelongsTo::make('Tenant')->showCreateRelationButton()->sortable(),
            Text::make('Domain')->sortable()->onlyOnForms()->rules([
                'required',
                'alpha_dash',
                Rule::unique('domains', 'domain')->ignore($this->id),
            ]),
            URL::make('Domain', 'url')->sortable()->displayUsing(function ($url) {
                return $url;
            })->exceptOnForms(),
            Boolean::make('Custom Subdomain', 'is_custom_subdomain'),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->sortable()->exceptOnForms(),
            DateTime::make('Updated At')->sortable()->exceptOnForms()->onlyOnDetail(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
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
        return [];
    }
}
