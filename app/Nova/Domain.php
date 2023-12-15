<?php

namespace App\Nova;

use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

class Domain extends Resource
{
    public static string $model = \App\Models\Domain::class;

    /**
     * @var array
     */
    public static $search = ['id', 'domain'];

    public function title(): ?string
    {
        return $this->url;
    }

    public function subtitle(): ?string
    {
        return "Tenant: {$this->tenant->name}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Tenant')
                ->showCreateRelationButton()
                ->sortable(),
            Text::make('Domain')
                ->sortable()
                ->onlyOnForms()
                ->rules([
                    'required',
                    'alpha_dash',
                    Rule::unique('domains', 'domain')
                        ->ignore($this->id),
                ]),
            URL::make('Domain', 'url')
                ->sortable()
                ->displayUsing(function ($url) {
                    return $url;
                })
                ->exceptOnForms(),
            Boolean::make('Custom Subdomain', 'is_custom_subdomain'),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->sortable()
                ->exceptOnForms(),
            DateTime::make('Updated At')
                ->sortable()
                ->exceptOnForms()
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
