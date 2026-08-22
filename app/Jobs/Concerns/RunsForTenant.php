<?php

declare(strict_types=1);

namespace App\Jobs\Concerns;

use App\Models\Tenant;

/**
 * Handles a RunsPerTenant job. With tenancy enabled the work is executed
 * inside the given tenant's context; when self-hosted it runs directly
 * against the central database.
 *
 * Failures propagate so the queue's normal retry/backoff and failed-job
 * handling apply; the batch's allowFailures() keeps the remaining tenants
 * running when one fails.
 *
 * @property-read ?int $tenantKey
 */
trait RunsForTenant
{
    use ConfiguresTenantQueue;

    abstract public function work(): void;

    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        if (! config('tenancy.enabled')) {
            $this->work();

            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(fn () => $this->work());
    }
}
