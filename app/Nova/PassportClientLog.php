<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;

class PassportClientLog extends Resource
{
    public static string $model = \App\Models\PassportClientLog::class;

    public static array $orderBy = ['created_at' => 'desc'];

    /**
     * @var string
     */
    public static $title = 'id';

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
        return 'oauth-logs';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            DateTime::make('Requested At', 'created_at')
                ->sortable(),
            new Panel('Client', [
                MorphTo::make('Performed By', 'causer', User::class),
                Text::make('IP Address', function (Activity $log) {
                    return $log->properties->get('ip');
                }),
            ]),
            new Panel('Request', [
                Badge::make('Method', function (Activity $log) {
                    return $log->properties->get('method') ?? 'No Method Logged';
                })
                    ->map([
                        'No Method Logged' => 'info',
                        Request::METHOD_HEAD => 'info',
                        Request::METHOD_DELETE => 'danger',
                        Request::METHOD_GET => 'info',
                        Request::METHOD_OPTIONS => 'info',
                        Request::METHOD_PATCH => 'info',
                        Request::METHOD_POST => 'success',
                        Request::METHOD_PUT => 'warning',
                    ])
                    ->sortable(),
                Text::make('Endpoint', function (Activity $log) {
                    return $log->properties->get('endpoint') ?? 'No Endpoint Logged';
                })
                    ->copyable()
                    ->sortable(),
                Code::make('Headers', function (Activity $log) {
                    return $log->properties->get('request_headers');
                })
                    ->language('vim')
                    ->onlyOnDetail(),
            ]),
            new Panel('Response', [
                Text::make('Status', function (Activity $log) {
                    $status = $log->properties->get('status');
                    if ($status) {
                        $message = Response::$statusTexts[(int) $status];

                        return "$status $message";
                    }

                    return 'No Status Logged';
                }),
                MorphTo::make('Subject')
                    ->onlyOnDetail(),
                Code::make('Content', function (Activity $log) {
                    return optional($log->properties->get('content'), function ($data) {
                        return json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                    });
                })
                    ->json()
                    ->onlyOnDetail(),
                Code::make('Headers', function (Activity $log) {
                    return $log->properties->get('response_headers');
                })
                    ->language('vim')
                    ->onlyOnDetail(),
            ]),
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
