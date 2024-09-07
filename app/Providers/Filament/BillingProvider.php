<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\CheckSubscription;
use Filament\Billing\Providers\SparkBillingProvider;

class BillingProvider extends SparkBillingProvider
{
    public function getSubscribedMiddleware(): string
    {
        return CheckSubscription::class;
    }
}
