<?php

namespace App\Nova\Lenses;

use App\Models\Records\Assignment;
use App\Models\Records\Award;
use App\Models\Records\Combat;
use App\Models\Records\Qualification;
use App\Models\Records\Rank;
use App\Models\Records\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;

class CurrentUsersRecords extends Lens
{
    /**
     * @return string
     */
    public function name()
    {
        return 'My Records';
    }

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        $serviceRecordsQuery = Service::query()
            ->select(['text', 'created_at', 'updated_at', 'person_id'])
            ->whereHas('person', function (Builder $query) {
                $query->whereHas('users', function (Builder $query) {
                    $query->where('user_id', '=', Auth::user()->getAuthIdentifier());
                });
            });

        $assignmentRecordQuery = Assignment::query()
            ->select(['text', 'created_at', 'updated_at', 'person_id'])
            ->whereHas('person', function (Builder $query) {
                $query->whereHas('users', function (Builder $query) {
                    $query->where('user_id', '=', Auth::user()->getAuthIdentifier());
                });
            });

        $awardsRecordQuery = Award::query()
            ->select(['text', 'created_at', 'updated_at', 'person_id'])
            ->whereHas('person', function (Builder $query) {
                $query->whereHas('users', function (Builder $query) {
                    $query->where('user_id', '=', Auth::user()->getAuthIdentifier());
                });
            });

        $combatRecordQuery = Combat::query()
            ->select(['text', 'created_at', 'updated_at', 'person_id'])
            ->whereHas('person', function (Builder $query) {
                $query->whereHas('users', function (Builder $query) {
                    $query->where('user_id', '=', Auth::user()->getAuthIdentifier());
                });
            });

        $qualificationRecordQuery = Qualification::query()
            ->select(['text', 'created_at', 'updated_at', 'person_id'])
            ->whereHas('person', function (Builder $query) {
                $query->whereHas('users', function (Builder $query) {
                    $query->where('user_id', '=', Auth::user()->getAuthIdentifier());
                });
            });

        $rankRecordQuery = Rank::query()
            ->select(['text', 'created_at', 'updated_at', 'person_id'])
            ->whereHas('person', function (Builder $query) {
                $query->whereHas('users', function (Builder $query) {
                    $query->where('user_id', '=', Auth::user()->getAuthIdentifier());
                });
            });

        return $request->withOrdering(
            $request->withFilters(
                $serviceRecordsQuery
                    ->union($assignmentRecordQuery)
                    ->union($awardsRecordQuery)
                    ->union($combatRecordQuery)
                    ->union($qualificationRecordQuery)
                    ->union($rankRecordQuery)
                    ->orderBy('created_at', 'DESC')
            )
        );
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [Text::make('Text')->sortable(), DateTime::make('Created At'), DateTime::make('Updated At')];
    }

    /**
     * Get the cards available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
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
        return 'current-users-records';
    }
}
