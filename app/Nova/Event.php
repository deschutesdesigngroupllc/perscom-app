<?php

namespace App\Nova;

use App\Nova\Actions\BatchNewEventRegistration;
use App\Nova\Actions\NewEventRegistration;
use App\Nova\Actions\RemoveEventRegistration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
    ];

    /**
     * @return string
     */
    public function title()
    {
        return $this->name.optional($this->calendar, static function ($calendar) {
            return " ($calendar->name)";
        });
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required')->showOnPreview(),
            BelongsTo::make('Calendar')->sortable()->rules('required')->showOnPreview(),
            BelongsTo::make('Organizer', 'author', User::class)->default(function (NovaRequest $request) {
                return $request->user()->getAuthIdentifier();
            })->rules('required')->sortable()->showOnPreview(),
            Tag::make('Tags')->showCreateRelationButton(),
            Textarea::make('Description')->alwaysShow()->hideFromIndex()->showOnPreview(),
            Textarea::make('Location')->alwaysShow()->hideFromIndex()->showOnPreview(),
            URL::make('URL')->hideFromIndex()->showOnPreview(),
            new Panel('Event', [
                Text::make('Starts', function () {
                    return optional($this->start, function ($start) {
                        return $this->all_day ? $start->toFormattedDayDateString() : $start->toDayDateTimeString();
                    });
                })->showOnPreview()->exceptOnForms(),
                Text::make('Ends', function () {
                    return optional($this->computed_end, function ($end) {
                        return $this->all_day ? $end->toFormattedDayDateString() : $end->toDayDateTimeString();
                    });
                })->showOnPreview()->exceptOnForms(),
                Boolean::make('All Day', 'all_day'),
                DateTime::make('Starts', 'start')
                    ->rules('required')
                    ->default(now())
                    ->onlyOnForms()
                    ->dependsOn(['all_day'], function (DateTime $field, NovaRequest $request, FormData $formData) {
                        if ($formData->all_day) {
                            $field->rules('')->hide();
                        }
                    }),
                Date::make('Starts', 'start')
                    ->hide()
                    ->default(now())
                    ->onlyOnForms()
                    ->dependsOn(['all_day'],
                        function (Date $field, NovaRequest $request, FormData $formData) {
                            if ($formData->all_day) {
                                $field->rules('required')->show();
                            }
                        }),
                DateTime::make('Ends', 'end')
                    ->rules(['required', 'after_or_equal:start'])
                    ->default(now()->addHour())
                    ->onlyOnForms()
                    ->dependsOn(['all_day', 'repeats'],
                        function (DateTime $field, NovaRequest $request, FormData $formData) {
                            if ($formData->all_day || $formData->repeats) {
                                $field->rules('')->hide();
                            }
                        }),
                Date::make('Ends', 'end')
                    ->hide()
                    ->default(now()->addHour())
                    ->onlyOnForms()
                    ->dependsOn(['all_day', 'repeats'], function (Date $field, NovaRequest $request, FormData $formData) {
                        if ($formData->all_day) {
                            $field->rules(['required', 'after_or_equal:start'])->show();
                        }

                        if ($formData->repeats) {
                            $field->rules('');
                        }
                    }),
                Boolean::make('Repeats', 'repeats'),
                Boolean::make('Has Passed', function () {
                    return $this->is_past;
                }),
                Text::make('Pattern', function () {
                    return Str::ucfirst($this->human_readable_pattern);
                })->onlyOnDetail(),
                Number::make('Every', 'interval')
                    ->hide()
                    ->help('The interval at which the event will repeat.')
                    ->default(1)
                    ->onlyOnForms()
                    ->dependsOn(['repeats'], function (Number $field, NovaRequest $request, FormData $formData) {
                        if ($formData->repeats) {
                            $field->rules('required')->show();
                        }
                    }),
                Select::make('Frequency', 'frequency')->options([
                    'DAILY' => 'Day(s)',
                    'WEEKLY' => 'Week(s)',
                    'MONTHLY' => 'Month(s)',
                    'YEARLY' => 'Year(s)',
                ])->default('WEEKLY')
                    ->hide()
                    ->help('The frequency at which the event will repeat.')
                    ->onlyOnForms()
                    ->dependsOn(['repeats'], function (Select $field, NovaRequest $request, FormData $formData) {
                        if ($formData->repeats) {
                            $field->rules('required')->show();
                        }
                    }),
                MultiSelect::make('On', 'by_day')
                    ->options([
                        'SU' => 'Sunday',
                        'MO' => 'Monday',
                        'TU' => 'Tuesday',
                        'WE' => 'Wednesday',
                        'TH' => 'Thursday',
                        'FR' => 'Friday',
                        'SA' => 'Saturday',
                    ])
                    ->hide()
                    ->help('The day(s) of the week will the event repeat.')
                    ->onlyOnForms()
                    ->dependsOn(['repeats', 'frequency'], function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                        if ($formData->repeats && $formData->frequency === 'WEEKLY') {
                            $field->show();
                        }
                    }),
                MultiSelect::make('On', 'by_month_day')
                    ->options([])
                    ->hide()
                    ->help('The day of the month will the event repeat.')
                    ->onlyOnForms()
                    ->dependsOn(['repeats', 'frequency', 'start'], function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                        if ($formData->repeats && $formData->frequency === 'MONTHLY') {
                            $field->show();
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
                    }),
                MultiSelect::make('In', 'by_month')
                    ->options([
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
                    ])
                    ->hide()
                    ->help('The month will the event repeat.')
                    ->onlyOnForms()
                    ->dependsOn(['repeats', 'frequency'], function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                        if ($formData->repeats && $formData->frequency === 'YEARLY') {
                            $field->show();
                        }
                    }),
                Select::make('Ends', 'end_type')->options([
                    'never' => 'Never',
                    'on' => 'On',
                    'after' => 'After',
                ])->default('never')
                    ->hide()
                    ->help('Specify when the event will end.')
                    ->onlyOnForms()
                    ->dependsOn(['repeats'], function (Select $field, NovaRequest $request, FormData $formData) {
                        if ($formData->repeats) {
                            $field->rules('required')->show();
                        }
                    }),
                Date::make('On', 'until')
                    ->hide()
                    ->default(now()->addMonth())
                    ->help('The event will end after the date specified above.')
                    ->onlyOnForms()
                    ->dependsOn(['end_type'], function (Date $field, NovaRequest $request, FormData $formData) {
                        if ($formData->end_type === 'on') {
                            $field->rules(['required', 'after_or_equal:start'])->show();
                        }
                    }),
                Number::make('After', 'count')
                    ->hide()
                    ->default(1)
                    ->help('The event will end after the number of occurences specified above.')
                    ->onlyOnForms()
                    ->dependsOn(['end_type'], function (Number $field, NovaRequest $request, FormData $formData) {
                        if ($formData->end_type === 'after') {
                            $field->rules('required')->show();
                        }
                    }),
            ]),
            new Panel('Details', [
                Trix::make('Content')->alwaysShow(),
            ]),
            new Panel('Registration', [
                Boolean::make('Registrations Enabled', 'registration_enabled')->default(true),
                DateTime::make('Registration Deadline', 'registration_deadline')->help('Leave blank to set no deadline.')->nullable(),
            ]),
            BelongsToMany::make('Registrations', 'registrations', User::class)
                ->fields(function () {
                    return [
                        DateTime::make('Registered At', 'created_at')->default(now()),
                    ];
                })
                ->referToPivotAs('registration'),
            MorphMany::make('Images'),
            MorphMany::make('Attachments'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new BatchNewEventRegistration())->canSee(function () {
                return Gate::check('create', \App\Models\EventRegistration::class);
            }),
            (new NewEventRegistration())->showInline()->canRun(function (NovaRequest $request, ?\App\Models\Event $event) {
                return ! $event?->registrations->contains($request->user());
            }),
            (new RemoveEventRegistration())->showInline()->canRun(function (NovaRequest $request, ?\App\Models\Event $event) {
                return $event?->registrations->contains($request->user());
            }),
        ];
    }
}
