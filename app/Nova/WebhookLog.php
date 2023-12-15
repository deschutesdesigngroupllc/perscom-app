<?php

namespace App\Nova;

use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Activitylog\Models\Activity;

class WebhookLog extends Resource
{
    public static string $model = \App\Models\WebhookLog::class;

    public static array $orderBy = ['created_at' => 'desc'];

    /**
     * @var string
     */
    public static $title = 'description';

    /**
     * @var array
     */
    public static $search = ['id'];

    public static function label(): string
    {
        return 'Logs';
    }

    public static function uriKey(): string
    {
        return 'webhook-logs';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Event', function (Activity $activity) {
                return $activity->getExtraProperty('event');
            })
                ->sortable(),
            MorphTo::make('Resource', 'causer')
                ->sortable(),
            DateTime::make('Created At')
                ->sortable(),
            Code::make('Payload', function (Activity $log) {
                return optional($log->properties, function ($data) {
                    return json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                });
            })
                ->json()
                ->onlyOnDetail(),
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
