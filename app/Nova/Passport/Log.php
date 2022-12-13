<?php

namespace App\Nova\Passport;

use App\Nova\Resource;
use App\Nova\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;

class Log extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Passport\Log::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id'];

    /**
     * @var string[]
     */
    public static $orderBy = ['created_at' => 'desc'];

    /**
     * @return string
     */
    public static function label()
    {
        return 'Logs';
    }

    /**
     * @return string
     */
    public static function uriKey()
    {
        return 'api-logs';
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
            Heading::make('Request'),
            Badge::make('Method', function (Activity $log) {
                return $log->properties->get('method') ?? 'No Method Logged';
            })->map([
                'No Method Logged' => 'info',
                Request::METHOD_HEAD => 'info',
                Request::METHOD_DELETE => 'danger',
                Request::METHOD_GET => 'info',
                Request::METHOD_OPTIONS => 'info',
                Request::METHOD_PATCH => 'info',
                Request::METHOD_POST => 'success',
                Request::METHOD_PUT => 'warning',
            ])->sortable(),
            Text::make('Endpoint', function (Activity $log) {
                return $log->properties->get('endpoint') ?? 'No Endpoint Logged';
            })->copyable()->sortable(),
            Code::make('Headers', function (Activity $log) {
                return $log->properties->get('request_headers');
            })->language('vim')->onlyOnDetail(),
            Heading::make('Client'),
            MorphTo::make('Performed By', 'causer', User::class),
            Text::make('IP Address', function (Activity $log) {
                return $log->properties->get('ip');
            }),
            Heading::make('Response'),
            Text::make('Status', function (Activity $log) {
                $status = $log->properties->get('status');
                if ($status) {
                    $message = Response::$statusTexts[$status];

                    return "$status $message";
                }

                return 'No Status Logged';
            }),
            MorphTo::make('Subject')->onlyOnDetail(),
            Code::make('Content', function () {
                return $this->description;
            })->json()->onlyOnDetail(),
            Code::make('Headers', function (Activity $log) {
                return $log->properties->get('response_headers');
            })->language('vim')->onlyOnDetail(),
            Heading::make('Meta'),
            DateTime::make('Requested At', 'created_at')->sortable(),
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
