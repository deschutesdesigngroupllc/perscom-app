<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResetTenantDatabaseFactory extends DestructiveAction implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

	/**
	 * The displayable name of the action.
	 *
	 * @var string
	 */
	public $name = 'Reset Database To Factory';

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
            Artisan::call('tenants:migrate-fresh', [
            	'--tenants' => $model->getTenantKey()
            ]);

	        Artisan::call('tenants:seed', [
		        '--tenants' => $model->getTenantKey()
	        ]);
        }
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
