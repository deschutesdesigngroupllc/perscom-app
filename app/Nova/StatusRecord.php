<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class StatusRecord extends Resource
{
    public static string $model = \App\Models\StatusRecord::class;

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'text'];

    public static function uriKey(): string
    {
        return 'status-records';
    }

    public static function label(): string
    {
        return 'Status Records';
    }

    public function subtitle(): ?string
    {
        return "Created At: {$this->created_at->toDayDateTimeString()}";
    }

    public function fields(NovaRequest $request): array
    {
        return [ID::make()
            ->sortable(), Textarea::make('Text')];
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
