<?php

declare(strict_types=1);

namespace App\Jobs\Concerns;

use App\Models\Tenant;

/**
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

        Tenant::findOrFail($this->tenantKey)->run(fn (): null => $this->work());
    }
}
