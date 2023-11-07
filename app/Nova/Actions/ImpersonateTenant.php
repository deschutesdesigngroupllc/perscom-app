<?php

namespace App\Nova\Actions;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

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
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $tenant) {
            $token = tenancy()->impersonate($tenant, $fields->user, $tenant->url, 'web');

            return Action::openInNewTab($tenant->route('tenant.impersonate', [
                'token' => $token->token,
            ]));
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        if ($request->resourceId) {
            $tenant = Tenant::findOrFail($request->resourceId);
            $options = $tenant->run(function () {
                return User::all()->pluck('name', 'id')->sort()->toArray();
            });
        }

        return [
            Select::make('User')
                ->options($options ?? [])
                ->rules('required')
                ->help('Select the user you would like to sign in as.'),
        ];
    }
}
