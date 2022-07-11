<?php

namespace App\Nova\Records;

use App\Nova\Metrics\NewAwardRecords;
use App\Nova\Metrics\TotalAwardRecords;
use App\Nova\Resource;
use App\Nova\User;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Award extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Records\Award::class;

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
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'award-records';
    }

    /**
     * @return string
     */
    public static function label()
    {
        return 'Award Records';
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->person->full_name;
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
            BelongsTo::make('Person')->sortable(),
            BelongsTo::make('Award')
                ->searchable()
                ->sortable()
	            ->showCreateRelationButton(),
            Textarea::make('Text')->alwaysShow(),
            Text::make('Text', function ($model) {
                return $model->text;
            })->onlyOnIndex(),
            new Panel('History', [
                BelongsTo::make('Author', 'author', User::class)->onlyOnDetail(),
                DateTime::make('Created At')
                    ->sortable()
                    ->exceptOnForms(),
                DateTime::make('Updated At')
                    ->exceptOnForms()
                    ->hideFromIndex(),
            ]),
            new Panel('Attachments', [BelongsTo::make('Document')->nullable()]),
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
        return [new TotalAwardRecords(), new NewAwardRecords()];
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
