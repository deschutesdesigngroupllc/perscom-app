<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
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
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $receipt) {
            if ($receipt instanceof Receipt) {
                $url = $receipt->owner->run(function ($tenant) use ($receipt) {
                    return URL::signedRoute('tenant.admin.download.receipt', [
                        'id' => $receipt->provider_id,
                    ], null, false);
                });

                return Action::openInNewTab($receipt->owner->url.$url);
            }
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
