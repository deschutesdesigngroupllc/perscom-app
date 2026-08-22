<?php

declare(strict_types=1);

namespace App\Actions\Tenancy;

use App\Contracts\RequiresTenancy;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

/**
 * Dispatches a per-tenant job across the whole deployment.
 *
 * - Tenancy enabled: batches the job once per tenant on the central connection.
 * - Self-hosted (tenancy disabled): RequiresTenancy jobs are skipped entirely;
 *   RunsPerTenant jobs run once against the central database.
 */
class DispatchTenantJob
{
    /**
     * @param  class-string  $job
     * @param  array<int, mixed>  $arguments  extra constructor arguments after the tenant key
     *
     * @throws Throwable
     */
    public static function for(string $job, string $name, string $queue = 'default', array $arguments = []): ?Batch
    {
        if (! config('tenancy.enabled')) {
            if (is_a($job, RequiresTenancy::class, true)) {
                return null;
            }

            dispatch(new $job(null, ...$arguments));

            return null;
        }

        return Bus::batch(
            Tenant::all()->map(fn (Tenant $tenant): object => new $job($tenant->getKey(), ...$arguments))
        )->name($name)
            ->onQueue($queue)
            ->onConnection('central')
            ->allowFailures()
            ->dispatch();
    }
}
