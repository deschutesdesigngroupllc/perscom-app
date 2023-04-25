<?php

namespace App\Nova\Actions;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class RemoveEventRegistration extends DestructiveAction
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var string
     */
    public $name = 'Unregister For Event';

    /**
     * @var string
     */
    public $confirmButtonText = 'Remove Registration';

    /**
     * @var string
     */
    public $confirmText = 'Are you sure you want to unregister for this/these event(s)?';

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            if ($model instanceof Event) {
                $model->registrations()->wherePivot('user_id', '=', request()->user()->getAuthIdentifier())->detach();
            }

            if ($model instanceof EventRegistration) {
                $model->delete();
            }
        }

        return Action::message('You have successfully unregistered from the/these event(s).');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
