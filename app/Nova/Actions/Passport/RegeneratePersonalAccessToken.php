<?php

namespace App\Nova\Actions\Passport;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class RegeneratePersonalAccessToken extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * @var string
     */
    public $name = 'Regenerate API Key';

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->update([
                'secret' => Str::random(40),
            ]);
        }

        return Action::message('The API key has been regenerated.');
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        return [];
    }
}
