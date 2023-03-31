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
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;

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
        'id',
        'name',
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
                DateTime::make('Starts', 'start')->rules('required')->dependsOn(['all_day'],
                    function (DateTime $field, NovaRequest $request, FormData $formData) {
                        if ($formData->all_day) {
                            $field->rules('')->hide();
                        }
                    })->default(now())->onlyOnForms(),
                Date::make('Starts', 'start')->hide()->dependsOn(['all_day'],
                    function (Date $field, NovaRequest $request, FormData $formData) {
                        if ($formData->all_day) {
                            $field->rules('required')->show();
                        }
                    })->default(now())->onlyOnForms(),
                DateTime::make('Ends', 'end')->rules(['required', 'after:start'])->dependsOn(['all_day', 'repeats'],
                    function (DateTime $field, NovaRequest $request, FormData $formData) {
                        if ($formData->all_day) {
                            $field->rules('')->hide();
                        }

                        if ($formData->repeats) {
                            $field->rules('');
                        }
                    })->default(now()->addHour())->onlyOnForms(),
                Date::make('Ends', 'end')
                    ->hide()
                    ->dependsOn(['all_day', 'repeats'], function (Date $field, NovaRequest $request, FormData $formData) {
                        if ($formData->all_day) {
                            $field->rules(['required', 'after:start'])->show();
                        }

                        if ($formData->repeats) {
                            $field->rules('');
                        }
                    })->default(now()->addHour())->onlyOnForms(),
                Boolean::make('Repeats', 'repeats'),
                Number::make('Every', 'interval')->hide()->dependsOn(['repeats'], function (Number $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats) {
                        $field->rules('required')->show();
                    }
                })->help('The interval at which the event will repeat.')
                      ->default(1)
                      ->onlyOnForms(),
                Select::make('Frequency', 'frequency')->options([
                    FrequencyTypeEnum::DAY->value => 'Day(s)',
                    FrequencyTypeEnum::WEEK->value => 'Week(s)',
                    FrequencyTypeEnum::MONTH->value => 'Month(s)',
                    FrequencyTypeEnum::YEAR->value => 'Year(s)',
                ])->default('WEEK')->hide()->dependsOn(['repeats'], function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats) {
                        $field->rules('required')->show();
                    }
                })->help('The frequency at which the event will repeat.')
                      ->onlyOnForms(),
                MultiSelect::make('On', 'repeat_day')
                           ->options([
                               WeekdayEnum::SUNDAY->value => 'Sunday',
                               WeekdayEnum::MONDAY->value => 'Monday',
                               WeekdayEnum::TUESDAY->value => 'Tuesday',
                               WeekdayEnum::WEDNESDAY->value => 'Wednesday',
                               WeekdayEnum::THURSDAY->value => 'Thursday',
                               WeekdayEnum::FRIDAY->value => 'Friday',
                               WeekdayEnum::SATURDAY->value => 'Saturday',
                           ])
                           ->hide()
                           ->dependsOn(['repeats', 'frequency'], function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                               if ($formData->repeats && $formData->frequency === FrequencyTypeEnum::WEEK->value) {
                                   $field->rules('required')->show();
                               }
                           })
                           ->help('The day(s) of the week will the event repeat.')
                           ->default([Str::upper(now()->dayName)])
                           ->onlyOnForms(),
                Select::make('On', 'repeat_month_day')
                      ->options([])
                      ->hide()
                      ->dependsOn(['repeats', 'frequency', 'start'], function (Select $field, NovaRequest $request, FormData $formData) {
                          if ($formData->repeats && $formData->frequency === FrequencyTypeEnum::MONTH->value) {
                              $field->rules('required')->show();
                          }

                          if ($start = $formData->start) {
                              $field->options(function () use ($start) {
                                  $month = Carbon::parse($start)->startOfMonth();
                                  $period = collect(
                                      $month->toPeriod($month->copy()->endOfMonth(), 1, 'day')->settings(
                                          ['monthOverflow' => false]
                                      )
                                  );

                                  return $period->mapWithKeys(function ($value, $key) {
                                      return [$value->format('j') => $value->format('jS')];
                                  });
                              });
                          }
                      }
                      )->help('The day of the month will the event repeat.')
                       ->default(now()->day)
                       ->onlyOnForms(),
                Select::make('Ends', 'end_type')->options([
                    FrequencyEndTypeEnum::NEVER->value => 'Never',
                    FrequencyEndTypeEnum::IN->value => 'On',
                    FrequencyEndTypeEnum::AFTER->value => 'After',
                ])->default('NEVER')->hide()->dependsOn(['repeats'], function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->repeats) {
                        $field->rules('required')->show();
                    }
                })->help('Specify when the event will end.')
                  ->onlyOnForms(),
                Date::make('On', 'until')
                    ->hide()
                    ->dependsOn(['end_type'], function (Date $field, NovaRequest $request, FormData $formData) {
                        if ($formData->end_type === FrequencyEndTypeEnum::IN->value) {
                            $field->rules('required')->show();
                        }
                    })
                    ->default(now()->addMonth())
                    ->help('The event will end after the date specified above.')
                    ->onlyOnForms(),
                Number::make('After', 'count')->hide()->dependsOn(['end_type'], function (Number $field, NovaRequest $request, FormData $formData) {
                    if ($formData->end_type === FrequencyEndTypeEnum::AFTER->value) {
                        $field->rules('required')->show();
                    }
                })->help('The event will end after the number of occurences specified above.')
                      ->default(1)->onlyOnForms(),
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
