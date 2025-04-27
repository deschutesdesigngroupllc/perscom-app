<?php

declare(strict_types=1);

namespace App\Actions\Batches;

use App\Jobs\SendMassEmail as SendMassEmailJob;
use App\Models\Mail;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Throwable;

class SendMassEmail
{
    /**
     * @throws Throwable
     */
    public static function handle(Mail $mail): ?Batch
    {
        if (filled($mail->sent_at) || (! $mail->send_now && blank($mail->send_at))) {
            return null;
        }

        /** @var Collection<Tenant> $recipients */
        $recipients = filled($mail->recipients)
            ? Collection::wrap($mail->recipients)->map(fn ($tenantId): Tenant => Tenant::find($tenantId))
            : Tenant::all();

        return Bus::batch(
            jobs: $recipients->map(fn (Tenant $tenant): SendMassEmailJob => new SendMassEmailJob($tenant, $mail))
        )->name(
            name: 'Send Mass Email'
        )->onConnection(
            connection: 'central'
        )->dispatch();
    }
}
