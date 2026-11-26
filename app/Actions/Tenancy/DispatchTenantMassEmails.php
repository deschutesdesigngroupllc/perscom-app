<?php

declare(strict_types=1);

namespace App\Actions\Tenancy;

use App\Jobs\Central\SendMassEmail;
use App\Models\Mail;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

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

        $recipients = filled($mail->recipients)
            ? Tenant::query()->whereKey($mail->recipients)->get()
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
