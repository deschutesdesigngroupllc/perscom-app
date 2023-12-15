<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class SubscriptionItem extends Resource
{
    public static string $model = \Laravel\Cashier\SubscriptionItem::class;

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
            ID::make()
                ->sortable(),
            Text::make('Stripe ID')
                ->readonly(function ($request) {
                    return $request->isUpdateOrUpdateAttachedRequest();
                })
                ->rules(['required']),
            Text::make('Stripe Price')
                ->readonly(function ($request) {
                    return $request->isUpdateOrUpdateAttachedRequest();
                })
                ->rules(['required']),
            Text::make('Stripe Price')
                ->readonly(function ($request) {
                    return $request->isUpdateOrUpdateAttachedRequest();
                })
                ->rules(['required']),
            Number::make('Quantity')
                ->rules(['required']),
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
