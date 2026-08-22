<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * A job that performs work scoped to a single organization's data.
 *
 * When tenancy is enabled the work runs once inside each tenant's context;
 * when the app is self-hosted (tenancy disabled) it runs once against the
 * central database, which holds the single organization's data.
 */
interface RunsPerTenant
{
    public function work(): void;
}
