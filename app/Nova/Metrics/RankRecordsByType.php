<?php

namespace App\Nova\Metrics;

use App\Models\Records\Rank;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class RankRecordsByType extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $query = Rank::query();
        if (! Gate::check('update', $request->findModel())) {
            $query = $query->where('user_id', $request->user()->id);
        }

        return $this->count($request, $query, 'type')->label(function ($value) {
            $labels = [
                Rank::RECORD_RANK_PROMOTION => 'Promotion',
                Rank::RECORD_RANK_DEMOTION => 'Demotion',
            ];

            return $labels[$value];
        })->colors([
            Rank::RECORD_RANK_PROMOTION => '#16A34A',
            Rank::RECORD_RANK_DEMOTION => '#DC2626',
        ]);
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'rank-records-by-type';
    }

    /**
     * Get the displayable name of the metric
     *
     * @return string
     */
    public function name()
    {
        return Str::singular(Str::title(setting('localization_ranks', 'Rank'))).' Records By Type';
    }
}
