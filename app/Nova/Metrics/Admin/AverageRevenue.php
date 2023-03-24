<?php

namespace App\Nova\Metrics\Admin;

use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;

class AverageRevenue extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $timezone = Nova::resolveUserTimezone($request) ?? $request->timezone ?? config('app.timezone');
        $range = $request->range ?? 1;

        $query = DB::table('receipts')->select(DB::raw('AVG( TRIM( REPLACE(amount, \'$\', \'\')) + 0.0) as total'));

        $currentRange = $this->currentRange($range, $timezone);
        $previousRange = $this->previousRange($range, $timezone);

        $previousValue = round(
            (clone $query)->whereBetween(
                'paid_at', $this->formatQueryDateBetween($previousRange)
            )->first()->total ?? 0,
            $this->roundingPrecision,
            $this->roundingMode
        );

        $currentValue = round(
            (clone $query)->whereBetween(
                'paid_at', $this->formatQueryDateBetween($currentRange)
            )->first()->total ?? 0,
            $this->roundingPrecision,
            $this->roundingMode
        );

        return $this->result($currentValue)->previous($previousValue)->dollars()->format('0,0.00');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date')
        ];
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        //return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'admin-average-revenue';
    }
}
