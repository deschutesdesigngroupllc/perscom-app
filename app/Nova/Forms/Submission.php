<?php

namespace App\Nova\Forms;

use App\Nova\Resource;
use App\Nova\Status;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Submission extends Resource
{
    use HasTabs;
    use HasActionsInTabs;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Forms\Submission::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

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
            Hidden::make('User', 'user_id')->default(function (NovaRequest $request) {
                return $request->user()->id;
            })->showOnDetail(),
            BelongsTo::make('Form')->showOnPreview(),
            BelongsTo::make('User')->showOnPreview()->default(function (NovaRequest $request) {
                return $request->user()->id;
            })->onlyOnForms()->nullable()->help('The user will be set to guest if left blank.'),
            Text::make('User', function () {
                return optional($this->user, function ($user) {
                    return $user->name;
                }) ?? 'Guest';
            }),
            Badge::make('Status', function () {
                return $this->status->name ?? 'none';
            })->types([
                'none' => 'bg-gray-100 text-gray-600',
                $this->status?->name => $this->status?->color,
            ])->label(function () {
                return $this->status->name ?? 'No Current Status';
            }),
            Code::make('Data')->hideFromIndex()->rules('json')->json(),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->exceptOnForms()->sortable(),
            DateTime::make('Updated At')->exceptOnForms()->sortable(),
            Tabs::make('Relations', [
                Tab::make('Status History', [
                    MorphToMany::make('Status', 'statuses', Status::class)
                        ->allowDuplicateRelations()
                        ->fields(function () {
                            return [
                                Textarea::make('Text'),
                                Text::make('Text', function ($model) {
                                    return $model->text;
                                }),
                                DateTime::make('Updated At')->sortable()->onlyOnIndex(),
                            ];
                        }),
                    ]),
                Tab::make('Logs', [$this->actionfield()]),
            ]),
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
