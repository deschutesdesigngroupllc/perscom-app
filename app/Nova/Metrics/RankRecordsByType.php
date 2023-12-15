<?php

namespace App\Nova\Metrics;

use App\Models\Enums\RankRecordType;
use App\Models\RankRecord;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class RankRecordsByType extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $colors = collect(RankRecordType::cases())
            ->mapWithKeys(fn (RankRecordType $case) => [$case->value => $case->getColor()])
            ->toArray();

        return $this->count($request, RankRecord::class, 'type')
            ->label(function ($value) {
                return RankRecordType::from($value)
                    ->getLabel();
            })
            ->colors($colors);
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        return now()->addMinutes(5);
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
