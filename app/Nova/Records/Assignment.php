<?php

namespace App\Nova\Records;

use App\Nova\Attachment;
use App\Nova\Metrics\NewAssignmentRecords;
use App\Nova\Metrics\TotalAssignmentRecords;
use App\Nova\Resource;
use App\Nova\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Perscom\DocumentViewerTool\DocumentViewerTool;

class Assignment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Records\Assignment::class;

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
        return 'assignment-records';
    }

    /**
     * @return string
     */
    public static function label()
    {
        return 'Assignment Records';
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->user->name;
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if (Gate::check('update', $request->findModel())) {
            return $query;
        }

        return $query->where('user_id', $request->user()->id);
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
            BelongsTo::make(Str::singular(Str::title(setting('localization_users', 'User'))), 'user', User::class)
                     ->sortable(),
            BelongsTo::make('Unit')->sortable()->showCreateRelationButton(),
            BelongsTo::make('Position')->sortable()->showCreateRelationButton(),
            BelongsTo::make('Specialty')->sortable()->showCreateRelationButton(),
            Textarea::make('Text')->alwaysShow(),
            Text::make('Text', function ($model) {
                return $model->text;
            })->onlyOnIndex(),
            BelongsTo::make('Document')->nullable()->onlyOnForms(),
            new Panel('History', [
                BelongsTo::make('Author', 'author', User::class)->onlyOnDetail(),
                DateTime::make('Created At')->sortable()->exceptOnForms(),
                DateTime::make('Updated At')->exceptOnForms()->hideFromIndex(),
            ]),
            (new DocumentViewerTool())->withTitle($this->document->name ?? null)->withContent($this->document
                ? $this->document->replaceContent($this->user, $this) : null),
            MorphMany::make('Attachments', 'attachments', Attachment::class),
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
        return [new TotalAssignmentRecords(), new NewAssignmentRecords()];
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
