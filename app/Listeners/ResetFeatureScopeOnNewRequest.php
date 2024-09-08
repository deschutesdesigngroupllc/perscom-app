<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Features\BaseFeature;
use Laravel\Octane\Events\RequestReceived;

class ResetFeatureScopeOnNewRequest
{
    public function handle(RequestReceived $event): void
    {
        BaseFeature::resetTenant();
    }
}
