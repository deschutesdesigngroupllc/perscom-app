<?php

namespace App\Nova\Metrics;

use App\Models\Records\Rank;
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
        return $this->count($request, Rank::class, 'type')
	        ->label(function ($value) {
	        	$labels = [
	        		Rank::RECORD_RANK_PROMOTION => 'Promotion',
			        Rank::RECORD_RANK_DEMOTION => 'Demotion'
		        ];
	        	return $labels[$value];
	        })
	        ->colors([
		        Rank::RECORD_RANK_PROMOTION => '#16A34A',
		        Rank::RECORD_RANK_DEMOTION => '#DC2626'
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
}
