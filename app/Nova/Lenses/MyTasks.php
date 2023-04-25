<?php

namespace App\Nova\Lenses;

use App\Models\Enums\TaskAssignmentStatus;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;

class MyTasks extends Lens
{
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->forUser($request->user())
        ));
    }

    /**
     * Get the fields available to the lens.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            BelongsTo::make('Task')->sortable(),
            Text::make('Description', function () {
                return Str::limit($this->task?->description);
            }),
            Badge::make('Status', function () {
                return $this->status?->value;
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
            DateTime::make('Assigned At')->sortable(),
            DateTime::make('Due At')->sortable(),
            DateTime::make('Completed At')->sortable(),
            DateTime::make('Expires At')->sortable(),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'my-tasks';
    }
}
