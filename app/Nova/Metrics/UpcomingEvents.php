<?php

namespace App\Nova\Metrics;

use App\Models\EventRegistration;
use Carbon\Carbon;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;

class UpcomingEvents extends Table
{
    /**
     * @var string
     */
    public $helpText = 'Your upcoming events.';

    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return EventRegistration::forUser($request->user())->get()->map(function (EventRegistration $registration) {
            return MetricTableRow::make()
                ->icon('calendar')
                ->title($registration->event?->name)
                ->subtitle(Carbon::parse($registration->event?->next_occurrence)->toDayDateTimeString())
                ->actions(function () use ($registration) {
                    return [
                        MenuItem::externalLink('Open Event', route('nova.pages.detail', [
                            'resource' => \App\Nova\EventRegistration::uriKey(),
                            'resourceId' => $registration->id,
                        ])),
                    ];
                });
        })->sortBy(function ($row) {
            return $row->subtitle;
        }, SORT_REGULAR, false)->take(2)->values()->all();
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
}
