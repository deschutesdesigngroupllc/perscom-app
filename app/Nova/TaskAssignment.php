<?php

namespace App\Nova;

use App\Models\Enums\TaskAssignmentStatus;
use App\Nova\Actions\MarkTaskComplete;
use App\Nova\Lenses\MyTasks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasManyThrough;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class TaskAssignment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\TaskAssignment>
     */
    public static $model = \App\Models\TaskAssignment::class;

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
    public static $search = [
        'id',
    ];

    /**
     * @var string[]
     */
    public static $orderBy = ['assigned_at' => 'desc'];

    /**
     * @return string
     */
    public function title()
    {
        return $this->id.optional($this->task, static function ($task) {
            return " - $task->title";
        });
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
            BelongsTo::make('Task')->sortable(),
            BelongsTo::make('User')->sortable(),
            BelongsTo::make('Assigned By', 'assigned_by', User::class)->default(Auth::user()->getAuthIdentifier()),
            Text::make('Description', function () {
                return Str::limit($this->task?->description);
            }),
            Badge::make('Status', function ($model) {
                return $model->status->value;
            })->map([
                TaskAssignmentStatus::TASK_ASSIGNED->value => 'info',
                TaskAssignmentStatus::TASK_COMPLETE->value => 'success',
                TaskAssignmentStatus::TASK_COMPLETE_EXPIRED->value => 'warning',
                TaskAssignmentStatus::TASK_COMPLETE_PAST_DUE->value => 'warning',
                TaskAssignmentStatus::TASK_EXPIRED->value => 'danger',
                TaskAssignmentStatus::TASK_PASTDUE->value => 'danger',
            ])->withIcons()->label(function ($value) {
                return Str::replace('_', ' ', $value);
            }),
            new Panel('Important Dates', [
                DateTime::make('Assigned At')->default(now())->sortable(),
                DateTime::make('Due At')->sortable(),
                DateTime::make('Completed At')->sortable(),
                DateTime::make('Expires At')->sortable(),
            ]),
            URL::make('Form', function () {
                return $this->task->form->url;
            })->displayUsing(function ($url) {
                return 'Click To Open Form';
            })->canSee(function () {
                return $this->task?->form;
            })->onlyOnDetail(),
            new Panel('Details', [
                Markdown::make('Instructions', function () {
                    return $this->task?->instructions;
                })->alwaysShow(),
            ]),
            HasManyThrough::make('Attachments'),
        ];
    }

    /**
     * @param  NovaRequest  $request
     * @param  Action  $action
     * @return bool
     */
    public function authorizedToRunAction(NovaRequest $request, Action $action)
    {
        return true;
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
        return [
            new MyTasks,
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new MarkTaskComplete())->showInline(),
        ];
    }
}
