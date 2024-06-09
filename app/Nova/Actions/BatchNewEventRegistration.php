<?php

namespace App\Nova\Actions;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class BatchNewEventRegistration extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var bool
     */
    public $standalone = true;

    /**
     * @var string
     */
    public $name = 'Batch Create Event Registration';

    /**
     * @var string
     */
    public $confirmText = 'Select the users to register for the event.';

    /**
     * @var string
     */
    public $confirmButtonText = 'Register Users';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        $event = Event::findOrFail($fields->event);

        foreach ($fields->users ?? [] as $userId) {
            $event->registrations()->attach([
                'user_id' => $userId,
            ]);
        }

        return Action::message('You have successfully registered the user(s).');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            MultiSelect::make('Users')
                ->options(
                    User::all()
                        ->pluck('name', 'id')
                        ->sort()
                )
                ->rules('required'),
            Select::make('Event')
                ->options(
                    Event::all()
                        ->mapWithKeys(fn ($event) => [$event->id => "$event->name ({$event->calendar?->name})"])
                        ->sort()
                )
                ->rules('required'),
        ];
    }
}
