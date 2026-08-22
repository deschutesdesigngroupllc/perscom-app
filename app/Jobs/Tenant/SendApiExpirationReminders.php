<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RunsPerTenant;
use App\Jobs\Concerns\RunsForTenant;
use App\Mail\Tenant\ApiExpirationReminder;
use App\Models\PassportToken;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendApiExpirationReminders implements RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    public function __construct(public ?int $tenantKey = null)
    {
        $this->configureForTenancy();
    }

    public function work(): void
    {
        // In tenancy the tenant is the mail recipient; self-hosted uses the
        // application's configured from-address (always present).
        $recipient = tenant() ?? config('mail.from.address');

        PassportToken::query()->whereDate('expires_at', now()->addMonth()->toDateString())->each(function (PassportToken $passportToken) use ($recipient): void {
            if (! is_null($passportToken->expires_at)) {
                Mail::to($recipient)->send(new ApiExpirationReminder(
                    name: $passportToken->name,
                    expiresAt: $passportToken->expires_at
                ));
            }
        });

        PassportToken::query()->whereDate('expires_at', now()->addDay()->toDateString())->each(function (PassportToken $passportToken) use ($recipient): void {
            if (! is_null($passportToken->expires_at)) {
                Mail::to($recipient)->send(new ApiExpirationReminder(
                    name: $passportToken->name,
                    expiresAt: $passportToken->expires_at
                ));
            }
        });
    }
}
