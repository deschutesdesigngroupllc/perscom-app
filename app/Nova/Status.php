<?php

namespace App\Nova;

use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Status extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Status::class;

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
     * @var string[]
     */
    public static $colors = [
        'bg-sky-100 text-sky-600' => 'Blue',
        'bg-gray-100 text-gray-600' => 'Gray',
        'bg-green-100 text-green-600' => 'Green',
        'bg-red-100 text-red-600' => 'Red',
        'bg-white-100 text-black-600' => 'White',
        'bg-yellow-100 text-yellow-600' => 'Yellow',
    ];

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel()
    {
        return 'Create A Status';
    }

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
            Badge::make('Color', function ($model) {
                return $model->color;
            })
                ->types(
                    collect(self::$colors)
                        ->mapWithKeys(function ($value, $key) {
                            return [$key => $key];
                        })
                        ->toArray()
                )
                ->label(function ($value) {
                    return self::$colors[$value];
                }),
            Select::make('Color')
                ->rules(['required'])
                ->showOnPreview()
                ->displayUsingLabels()
                ->onlyOnForms()
                ->options(self::$colors),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->onlyOnDetail(),
            DateTime::make('Updated At')->onlyOnDetail(),
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
