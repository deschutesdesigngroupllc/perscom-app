<?php

declare(strict_types=1);

namespace App\Jobs\Concerns;

trait ConfiguresTenantQueue
{
    protected function configureForTenancy(?string $queue = null, ?string $connection = 'central'): void
    {
        if (! config('tenancy.enabled')) {
            return;
        }

        if ($queue !== null) {
            $this->onQueue($queue);
        }

        if ($connection !== null) {
            $this->onConnection($connection);
        }
    }
}
