<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Pennant\Feature;

class ActivateFeature extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var bool
     */
    public $showInline = true;

    /**
     * @var string
     */
    public $confirmText = 'Are you sure you want to activate this/these feature(s)?';

    /**
     * @var string
     */
    public $confirmButtonText = 'Activate Feature';

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            Feature::for($model->scope)
                ->activate($model->name);
        }
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        return [];
    }
}
