<?php

declare(strict_types=1);

namespace App\Jobs\System;

use App\Actions\Tenant\SetupTenantAccount as SetupTenantAccountAction;
use App\Contracts\RequiresTenancy;
use App\Mail\System\TenantSetupDelayedMail;
use App\Models\Admin;
use App\Models\Tenant;
use App\Notifications\Admin\TenantSetupFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Throwable;

class SetupTenantAccount implements RequiresTenancy, ShouldQueue
{
    use Queueable;

    public function __construct(protected Tenant $tenant)
    {
        //
    }

    public function handle(): void
    {
        /** @var SetupTenantAccountAction $action */
        $action = resolve(SetupTenantAccountAction::class);
        $action->handle($this->tenant);
    }

    /**
     * A failed setup must never leave the founder waiting in silence: the PERSCOM team is alerted with a
     * link to retry, and the founder is told what's happening.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Failed to setup tenant account', [
            'tenant' => $this->tenant->getKey(),
            'exception' => $exception,
        ]);

        Notification::send(Admin::all(), new TenantSetupFailed($this->tenant, Str::limit($exception->getMessage(), 500)));

        if (filled($this->tenant->email)) {
            Mail::to($this->tenant->email)->send(new TenantSetupDelayedMail($this->tenant));
        }
    }
}
