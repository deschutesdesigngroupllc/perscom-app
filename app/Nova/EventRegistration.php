<?php

namespace App\Nova;

use App\Nova\Actions\BatchNewEventRegistration;
use App\Nova\Actions\RemoveEventRegistration;
use App\Nova\Lenses\MyEvents;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
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
    public static string $model = \App\Models\EventRegistration::class;

    /**
     * @var string
     */
    public static $title = 'id';

    /**
     * @var array
     */
    public static $search = ['id'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Event')
                ->sortable(),
            BelongsTo::make('Calendar')
                ->sortable()
                ->exceptOnForms(),
            BelongsTo::make('User')
                ->default(function (NovaRequest $request) {
                    return $request->user()
                        ->getAuthIdentifier();
                })
                ->readonly(function () {
                    return ! Gate::check('create', $this->model());
                })
                ->sortable(),
            Text::make('Description', function () {
                return Str::limit($this->event?->description);
            }),
            Textarea::make('Location', function () {
                return $this->event?->location;
            })
                ->alwaysShow()
                ->hideFromIndex(),
            URL::make('URL', function () {
                return $this->event?->url;
            })
                ->hideFromIndex(),
            DateTime::make('Registered', 'created_at')
                ->exceptOnForms()
                ->sortable(),
            new Panel('Event', [
                Text::make('Starts', function () {
                    return optional($this->event?->start, function ($start) {
                        return $this->event?->all_day ? $start->toFormattedDayDateString() : $start->toDayDateTimeString();
                    });
                })
                    ->exceptOnForms(),
                Text::make('Ends', function () {
                    return optional($this->event?->computed_end, function ($end) {
                        return $this->event?->all_day ? $end->toFormattedDayDateString() : $end->toDayDateTimeString();
                    });
                })
                    ->exceptOnForms(),
                Boolean::make('All Day', function () {
                    return $this->event?->all_day;
                })
                    ->onlyOnDetail(),
                Boolean::make('Repeats', function () {
                    return $this->event?->repeats;
                })
                    ->onlyOnDetail(),
                Boolean::make('Has Passed', function () {
                    return $this->event?->is_past;
                })
                    ->onlyOnDetail(),
                Text::make('Pattern', function () {
                    return Str::ucfirst($this->event?->human_readable_pattern);
                })
                    ->onlyOnDetail(),
            ]),
            new Panel('Details', [
                Trix::make('Content', function () {
                    return $this->event?->content;
                })
                    ->alwaysShow(),
            ]),
            MorphMany::make('Images'),
            MorphMany::make('Attachments'),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [
            new MyEvents(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            (new BatchNewEventRegistration())->canSee(function () {
                return Gate::check('create', \App\Models\EventRegistration::class);
            }),
            (new RemoveEventRegistration())->showInline(),
        ];
    }
}
