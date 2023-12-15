<?php

namespace App\Nova;

use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Banner extends Resource
{
    public static string $model = \App\Models\Banner::class;

    /**
     * @var string
     */
    public static $title = 'message';

    /**
     * @var array
     */
    public static $search = ['id', 'message'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Message')
                ->rules(['required'])
                ->sortable(),
            URL::make('Link URL')
                ->sortable(),
            Text::make('Link Text')
                ->sortable(),
            Color::make('Background Color')
                ->sortable(),
            Color::make('Text Color')
                ->sortable(),
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
