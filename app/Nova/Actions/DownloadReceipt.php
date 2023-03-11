<?php

namespace App\Nova\Actions;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spark\Receipt;

class DownloadReceipt extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var bool
     */
    public $withoutConfirmation = true;

    /**
     * @var bool
     */
    public $showInline = true;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            if ($model instanceof Receipt) {
                $tenant = Tenant::findOrFail($model->tenant_id);

                $token = tenancy()->impersonate(
                    $tenant,
                    $fields->user,
                    $tenant->url.route('spark.receipts.download', [
                        'tenant',
                        $model->tenant_id,
                        $model->provider_id,
                    ], false),
                    'web'
                );

                return Action::openInNewTab("{$tenant->url}/impersonate/{$token->token}");
            }
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
