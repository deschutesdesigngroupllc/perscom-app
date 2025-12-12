<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Mail\Tenant\ApiExpirationReminder;
use App\Models\PassportToken;
use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendApiExpirationReminders implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public int $tenantKey)
    {
        $this->onConnection('central');
    }

    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(function (Tenant $tenant): void {
            PassportToken::query()->whereDate('expires_at', now()->addMonth()->toDateString())->each(function (PassportToken $passportToken) use ($tenant): void {
                if (! is_null($passportToken->expires_at)) {
                    Mail::to($tenant)->send(new ApiExpirationReminder(
                        name: $passportToken->name,
                        expiresAt: $passportToken->expires_at
                    ));
                }
            });

            PassportToken::query()->whereDate('expires_at', now()->addDay()->toDateString())->each(function (PassportToken $passportToken) use ($tenant): void {
                if (! is_null($passportToken->expires_at)) {
                    Mail::to($tenant)->send(new ApiExpirationReminder(
                        name: $passportToken->name,
                        expiresAt: $passportToken->expires_at
                    ));
                }
            });
        });
    }
}
