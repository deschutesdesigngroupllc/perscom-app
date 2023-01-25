<?php

namespace App\Nova\Fields;

use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;

class TaskAssignmentFields
{
    /**
     * Get the pivot fields for the relationship.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Model     $relatedModel
     *
     * @return array
     */
    public function __invoke($request, $relatedModel)
    {
        $users = \App\Models\User::all()->pluck('name', 'id')->sort()->toArray();

        return [
            Select::make('Assigned By', 'assigned_by_id')->displayUsing(function ($id) use ($users) {
                if (\array_key_exists($id, $users)) {
                    return $users[$id];
                }

                return null;
            })->options($users)->default(Auth::user()->getAuthIdentifier()),
            DateTime::make('Assigned At')->default(now()),
            DateTime::make('Due At')->nullable()->help('Set to assign a due date to the task.'),
            DateTime::make('Expires At')
                    ->nullable()
                    ->help('Set to assign an expiration date if the task is not completed.'),
        ];
    }
}
