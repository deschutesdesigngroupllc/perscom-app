<?php

declare(strict_types=1);

namespace App\Jobs\Concerns;

/**
 * Routes a job onto its dedicated tenant queue/connection only when tenancy is
 * enabled. Self-hosted deployments run everything on the default queue.
 */
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
