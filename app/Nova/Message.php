<?php

namespace App\Nova;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class Message extends Resource
{
    use HasSortableRows;

    public static string $model = \App\Models\Message::class;

    public static array $orderBy = ['order' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'title';

    /**
     * @var array
     */
    public static $search = ['id', 'title', 'message'];

    public function subtitle(): ?string
    {
        return $this->active ? 'Active: True' : 'Active: False';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Title')
                ->sortable()
                ->rules('required'),
            Textarea::make('Message')
                ->alwaysShow()
                ->rules('required'),
            Boolean::make('Active')
                ->sortable(),
            new Panel('Link', [
                Text::make('Text', 'link_text')
                    ->hideFromIndex(),
                URL::make('URL')
                    ->nullable(),
            ]),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->exceptOnForms()
                ->sortable(),
            DateTime::make('Updated At')
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
