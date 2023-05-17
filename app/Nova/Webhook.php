<?php

namespace App\Nova;

use App\Models\Enums\WebhookEvent;
use App\Models\Enums\WebhookMethod;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Webhook extends Resource
{
    use HasTabs;
    use HasActionsInTabs;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Webhook>
     */
    public static $model = \App\Models\Webhook::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'url';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'url',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            URL::make('URL')->rules('required')->onlyOnForms(),
            Text::make('URL', function () {
                return $this->url;
            })->copyable()->exceptOnForms()->sortable(),
            Textarea::make('Description'),
            Select::make('Method')
                ->options(collect(WebhookMethod::cases())->mapWithKeys(function (WebhookMethod $method) {
                    return [$method->value => Str::upper($method->value)];
                }))
                ->default(WebhookMethod::POST->value)
                ->rules('required')
                ->help('The HTTP method the webhook request will use.')
                ->onlyOnForms(),
            Badge::make('Method', function () {
                return Str::upper($this->method?->value);
            })->map([
                'POST' => 'info',
                'GET' => 'info',
            ])->exceptOnForms(),
            MultiSelect::make('Events')
                ->options(collect(WebhookEvent::cases())->mapWithKeys(function (WebhookEvent $method) {
                    return [$method->value => $method->value];
                })->sortKeys())
                ->rules('required')
                ->help('The events the webhook will listen to.'),
            Text::make('Secret')
                ->rules('required')
                ->default(Str::random())
                ->help('The secret that will be used to sign the webhook.')
                ->hideFromIndex(),
            Tabs::make('Logs', [
                Tab::make('Logs', [
                    MorphMany::make('Logs', 'logs', WebhookLog::class),
                ]),
                Tab::make('Actions', [
                    $this->actionfield(),
                ]),
            ]),
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
