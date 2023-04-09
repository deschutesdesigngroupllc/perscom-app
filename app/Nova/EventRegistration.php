<?php

namespace App\Nova;

use App\Nova\Lenses\MyEvents;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class EventRegistration extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\EventRegistration>
     */
    public static $model = \App\Models\EventRegistration::class;

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
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Event')->sortable(),
            BelongsTo::make('Calendar')->sortable(),
            BelongsTo::make('Organizer', 'organizer', User::class)->sortable(),
            BelongsTo::make('User')->sortable(),
            Text::make('Description', function () {
                return Str::limit($this->event?->description);
            }),
            Textarea::make('Location', function () {
                return $this->event?->location;
            })->alwaysShow()->hideFromIndex(),
            URL::make('URL', function () {
                return $this->event?->url;
            })->hideFromIndex(),
            new Panel('Event', [
                Text::make('Starts', function () {
                    return optional($this->event?->start, function ($start) {
                        return $this->event?->all_day ? $start->toFormattedDayDateString() : $start->toDayDateTimeString();
                    });
                })->exceptOnForms(),
                Text::make('Ends', function () {
                    return optional($this->event?->end, function ($end) {
                        return $this->event?->all_day ? $end->toFormattedDayDateString() : $end->toDayDateTimeString();
                    });
                })->exceptOnForms(),
                Boolean::make('All Day', function () {
                    return $this->event?->all_day;
                }),
                Boolean::make('Repeats', function () {
                    return $this->event?->repeats;
                }),
                Boolean::make('Has Passed', function () {
                    return $this->event?->is_past;
                }),
                Text::make('Pattern', function () {
                    return Str::ucfirst($this->event?->human_readable_pattern);
                })->onlyOnDetail(),
            ]),
            new Panel('Details', [
                Trix::make('Content', function () {
                    return $this->event?->content;
                })->alwaysShow(),
            ]),
            MorphMany::make('Images'),
            MorphMany::make('Attachments'),
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
        return [
            new MyEvents(),
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
        return [];
    }
}
