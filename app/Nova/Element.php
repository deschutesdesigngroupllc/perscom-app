<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

class Element extends Resource
{
    public static string $model = \App\Models\Element::class;

    /**
     * @var string
     */
    public static $title = 'id';

    /**
     * @var array
     */
    public static $search = ['id'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
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
