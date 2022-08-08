<?php

namespace App\Nova\Actions;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\URL;

class ImpersonateTenant extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Sign In To Tenant';

    /**
     * @var string
     */
    public $confirmButtonText = 'Sign In';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $tenant) {
            $token = tenancy()->impersonate($tenant, $fields->user, $tenant->url . '/dashboards/main', 'web');
            return Action::openInNewTab("{$tenant->url}/impersonate/{$token->token}");
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
	    if ($request->resourceId) {
		    $tenant = Tenant::findOrFail($request->resourceId);
		    $options = $tenant->run(function ($tenant) {
			    return User::all()
				    ->mapWithKeys(function ($user) {
					    return [$user->id => $user->name];
				    })
				    ->sort()
				    ->toArray();
		    });
	    }

        return [
            Select::make('User')
                ->options($options ?? [])
                ->required()
                ->help('Select the user you would like to sign in as.'),
        ];
    }
}
