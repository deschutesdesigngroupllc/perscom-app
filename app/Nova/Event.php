<?php

namespace App\Nova;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Event extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Event>
     */
    public static $model = \App\Models\Event::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name',
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
            Text::make('Name')->sortable()->rules('required'),
            BelongsTo::make('Calendar')->sortable()->rules('required'),
            Tag::make('Tags')->showCreateRelationButton(),
            Textarea::make('Description')->alwaysShow()->hideFromIndex(),
            Textarea::make('Location')->alwaysShow()->hideFromIndex(),
            URL::make('URL')->hideFromIndex(),
            new Panel('Event', [
                Text::make('Starts', function () {
                    return optional($this->start, function ($start) {
                        return $this->all_day ? $start->toFormattedDayDateString() : $start->toDayDateTimeString();
                    });
                })->exceptOnForms(),
                Text::make('Ends', function () {
                    return optional($this->end, function ($end) {
                        return $this->all_day ? $end->toFormattedDayDateString() : $end->toDayDateTimeString();
                    });
                })->exceptOnForms(),
                Boolean::make('All Day', 'all_day'),
                DateTime::make('Starts', 'start')->rules('required')->dependsOn(['all_day'], function (DateTime $field, NovaRequest $request, FormData $formData) {
                    if ($formData->all_day) {
                        $field->rules('')->hide();
                    }
                })->onlyOnForms(),
                Date::make('Starts', 'start')->hide()->dependsOn(['all_day'], function (Date $field, NovaRequest $request, FormData $formData) {
                    if ($formData->all_day) {
                        $field->rules('required')->show();
                    }
                })->onlyOnForms(),
                DateTime::make('Ends', 'end')->rules('required')->dependsOn(['all_day', 'repeats'], function (DateTime $field, NovaRequest $request, FormData $formData) {
                    if ($formData->all_day) {
                        $field->rules('')->hide();
                    }

                    if ($formData->repeats) {
                        $field->rules('');
                    }
                })->onlyOnForms(),
                Date::make('Ends', 'end')->hide()->dependsOn(['all_day', 'repeats'], function (Date $field, NovaRequest $request, FormData $formData) {
                    if ($formData->all_day) {
                        $field->rules('required')->show();
                    }

                    if ($formData->repeats) {
                        $field->rules('');
                    }
                })->onlyOnForms(),
                Boolean::make('Repeats', 'repeats'),
                Text::make('Schedule', function () {
                    return Str::ucfirst($this->recurrence_rule_string);
                })->hide()->dependsOn(['repeats'], function (Number $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats) {
                        $field->show();
                    }
                })->exceptOnForms(),
                Number::make('Every', 'interval')->hide()->dependsOn(['repeats'], function (Number $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats) {
                        $field->rules('required')->show();
                    }
                })->default(1)->onlyOnForms(),
                Select::make('Frequency', 'frequency')->options([
                    'DAILY' => 'Day(s)',
                    'WEEKLY' => 'Week(s)',
                    'MONTHLY' => 'Month(s)',
                    'YEARLY' => 'Year(s)',
                ])->default('WEEKLY')->hide()->dependsOn(['repeats'], function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats) {
                        $field->rules('required')->show();
                    }
                })->onlyOnForms(),
                MultiSelect::make('Each', 'by_month_day')->options([])->hide()->dependsOn(['repeats', 'frequency', 'start'], function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats && $formData->frequency === 'MONTHLY') {
                        $field->rules('required')->show();
                    }

                    if ($start = $formData->start) {
                        $field->options(function () use ($start) {
                            $month = Carbon::parse($start)->startOfMonth();
                            $period = collect($month->toPeriod($month->copy()->endOfMonth(), 1, 'day')->settings(['monthOverflow' => false]));

                            return $period->mapWithKeys(function ($value, $key) {
                                return [$value->format('j') => $value->format('jS')];
                            });
                        });
                    }
                })->onlyOnForms(),
                MultiSelect::make('In', 'by_month')->options([
                    '1' => 'January',
                    '2' => 'February',
                    '3' => 'March',
                    '4' => 'April',
                    '5' => 'May',
                    '6' => 'June',
                    '7' => 'July',
                    '8' => 'August',
                    '9' => 'September',
                    '10' => 'October',
                    '11' => 'November',
                    '12' => 'December',
                ])->hide()->dependsOn(['repeats', 'frequency'], function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats && $formData->frequency === 'YEARLY') {
                        $field->rules('required')->show();
                    }
                })->onlyOnForms(),
                MultiSelect::make('On', 'by_month_position')->options([
                    '1' => 'First',
                    '2' => 'Second',
                    '3' => 'Third',
                    '4' => 'Fourth',
                ])->hide()->dependsOn(['repeats', 'frequency'], function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats && ($formData->frequency === 'MONTHLY' || $formData->frequency === 'YEARLY')) {
                        $field->rules('required')->show();
                    }
                })->onlyOnForms(),
                MultiSelect::make('Day', 'by_day')->options([
                    'SU' => 'Sunday',
                    'MO' => 'Monday',
                    'TU' => 'Tuesday',
                    'WE' => 'Wednesday',
                    'TH' => 'Thursday',
                    'FR' => 'Friday',
                    'SA' => 'Saturday',
                ])->hide()->dependsOn(['repeats', 'frequency'], function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats && $formData->frequency !== 'DAILY') {
                        $field->rules('required')->show();
                    }
                })->onlyOnForms(),
            ]),
            new Panel('Details', [
                Trix::make('Content')->alwaysShow(),
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
        return [];
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
