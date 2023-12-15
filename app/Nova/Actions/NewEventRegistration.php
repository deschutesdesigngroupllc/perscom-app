<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class NewEventRegistration extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var string
     */
    public $name = 'Register For Event';

    /**
     * @var string
     */
    public $confirmButtonText = 'Register';

    /**
     * @var string
     */
    public $confirmText = 'Are you sure you want to register for this/these event(s)?';

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->registrations()
                ->attach([
                    'user_id' => request()
                        ->user()
                        ->getAuthIdentifier(),
                ]);
        }

        return Action::message('You have successfully registered for the/these event(s).');
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        return [];
    }
}
