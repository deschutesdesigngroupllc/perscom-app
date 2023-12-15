<?php

namespace App\Nova;

use App\Nova\Actions\ActivateFeature;
use App\Nova\Actions\DeactivateFeature;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class Feature extends Resource
{
    public static string $model = \App\Models\Feature::class;

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public static function createButtonLabel(): string
    {
        return 'Add Feature';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Tenant')
                ->sortable(),
            Select::make('Feature', 'name')
                ->rules('required')
                ->sortable()
                ->options(\App\Models\Feature::options()),
            Boolean::make('Activated', 'value')
                ->trueValue('true')
                ->falseValue('false')
                ->rules('required')
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
        return [
            new ActivateFeature(),
            new DeactivateFeature(),
        ];
    }
}
