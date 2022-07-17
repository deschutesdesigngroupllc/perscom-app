<?php

namespace App\Nova\Forms;

use App\Nova\Field;
use App\Nova\Resource;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Form extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Forms\Form::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'name'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')
                ->sortable()
                ->rules(['required'])
                ->showOnPreview(),
            Slug::make('Slug')
                ->from('Name')
                ->rules(['required', 'unique:forms,slug']),
            URL::make('URL')
                ->displayUsing(function ($url) {
                    return $url;
                })
                ->readonly(),
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Markdown::make('Instructions'),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->onlyOnDetail(),
            DateTime::make('Updated At')->onlyOnDetail(),
            MorphToMany::make('Fields', 'fields', Field::class)->fields(function ($request, $relatedModel) {
                return [Number::make('Order')->sortable()];
            }),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
