<?php

namespace App\Nova\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Stancl\Tenancy\Events\TenantDeleted;

class DeleteTenantDatabase extends DestructiveAction
{
    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Delete Database';

    /**
     * @var string
     */
    public $confirmButtonText = 'Delete Database';

    /**
     * @var string
     */
    public $confirmText = 'Are you sure you want to delete this tenant\'s database?';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            Event::dispatch(new TenantDeleted($model));
            $model->update([
                'tenancy_db_name' => null,
            ]);
        }

        return Action::message('The tenant\'s database has been deleted.');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
