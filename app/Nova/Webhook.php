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
    use HasActionsInTabs;
    use HasTabs;

    public static string $model = \App\Models\Webhook::class;

    /**
     * @var string
     */
    public static $title = 'url';

    /**
     * @var array
     */
    public static $search = ['id', 'url'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            URL::make('URL')
                ->rules('required')
                ->onlyOnForms(),
            Text::make('URL', function () {
                return $this->url;
            })
                ->copyable()
                ->exceptOnForms()
                ->sortable(),
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
            })
                ->map([
                    'POST' => 'info',
                    'GET' => 'info',
                ])
                ->exceptOnForms(),
            MultiSelect::make('Events')
                ->options(
                    collect(WebhookEvent::cases())
                        ->mapWithKeys(function (WebhookEvent $method) {
                            return [$method->value => $method->value];
                        })
                        ->sortKeys()
                )
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
            ])
                ->showTitle(),
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
