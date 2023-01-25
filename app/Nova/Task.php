<?php

namespace App\Nova;

use App\Nova\Fields\TaskAssignmentFields;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Task extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Task>
     */
    public static $model = \App\Models\Task::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'title'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Title')->rules('required'),
            Textarea::make('Description')->alwaysShow(),
            Text::make('Description', function () {
                return $this->description;
            })->onlyOnIndex(),
            Markdown::make('Instructions')->nullable()->help('Set to add some instructions to the task.'),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->sortable()->onlyOnDetail(),
            DateTime::make('Updated At')->sortable()->onlyOnDetail(),
            new Panel('Details', [
                BelongsTo::make('Form')
                         ->nullable()
                         ->help('Set to assign a form that needs to be completed as apart of the task.')
                         ->hideFromIndex()
            ]),
            BelongsToMany::make('Assigned To', 'users', User::class)
                         ->fields(new TaskAssignmentFields)
                         ->referToPivotAs('assignment'),
            MorphMany::make('Attachments')
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
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
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
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
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
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
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
