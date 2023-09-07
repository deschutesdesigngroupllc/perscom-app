<?php

namespace App\Nova\Actions;

use App\Jobs\Tenant\CreateInitialTenantUser;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResetTenantDatabaseFactory extends DestructiveAction implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Reset Database To Factory';

    /**
     * @var string
     */
    public $confirmButtonText = 'Reset Tenant';

    /**
     * @var string
     */
    public $confirmText = 'Are you sure you want to reset this tenant\'s database to factory?';

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            Artisan::call('tenants:migrate-fresh', [
                '--tenants' => $model->getTenantKey(),
            ]);

            Artisan::call('tenants:seed', [
                '--tenants' => $model->getTenantKey(),
            ]);

            if ($fields->new_subdomain) {
                $model->domains()->delete();
                $model->domains()->create([
                    'domain' => $fields->subdomain ?? Domain::generateSubdomain(),
                ]);
            }

            if ($fields->remove_subscription) {
                foreach ($model->subscriptions as $subscription) {
                    $subscription->cancelNow();
                }
            }

            if ($fields->new_admin) {
                CreateInitialTenantUser::dispatch($model, $fields->send_email);
            }
        }

        return Action::message('The tenant\'s database has been reset to factory.');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Boolean::make('Reset Subdomain', 'new_subdomain')->help('Reset the tenant\'s subdomain.'),
            Text::make('Subdomain', 'subdomain')
                ->hide()
                ->help('The tenant\'s new subdomain. Leave blank to auto-generate.')
                ->dependsOn(['new_subdomain'], static function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->new_subdomain) {
                        $field->show();
                    }
                }),
            Boolean::make('New Admin', 'new_admin')
                ->default(true)
                ->help('This will create a new admin user after the reset finishes.'),
            Boolean::make('Send Email', 'send_email')
                ->hide()
                ->help('Send the "Your Organization Is Now Ready" email.')
                ->dependsOn(['new_admin'], static function (Boolean $field, NovaRequest $request, FormData $formData) {
                    if ($formData->new_admin) {
                        $field->show();
                    }
                }),
            Boolean::make('Remove Subscription', 'remove_subscription')
                ->default(true)
                ->help('Remove and cancel all subscriptions.'),
        ];
    }
}
