<?php

declare(strict_types=1);

namespace App\Actions\Tenancy;

use App\Jobs\Central\SendMassEmail;
use App\Models\Mail;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Throwable;

/**
 * Sends an operator mass-email to tenants. This only applies to the multi-tenant
 * SaaS deployment; self-hosted installs have no tenant audience.
 */
class DispatchTenantMassEmails
{
    /**
     * @throws Throwable
     */
    public static function handle(Mail $mail): ?Batch
    {
        if (! config('tenancy.enabled')) {
            return null;
        }

        if (filled($mail->sent_at) || (! $mail->send_now && blank($mail->send_at))) {
            return null;
        }

        /** @var Collection<Tenant> $recipients */
        $recipients = filled($mail->recipients)
            ? Collection::wrap($mail->recipients)->map(fn ($tenantId): Tenant => Tenant::find($tenantId))
            : Tenant::all();

        return Bus::batch(
            jobs: $recipients->map(fn (Tenant $tenant): SendMassEmail => new SendMassEmail($tenant, $mail))
        )->name(
            name: 'Send Tenant Mass Emails'
        )->onConnection(
            connection: 'central'
        )->allowFailures()->dispatch();
    }
}
