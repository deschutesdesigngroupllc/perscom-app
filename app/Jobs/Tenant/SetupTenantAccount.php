<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Actions\Tenant\SetupTenantAccount as SetupTenantAccountAction;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SetupTenantAccount implements ShouldQueue
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

    public function failed(Throwable $exception): void
    {
        Log::error('Failed to setup tenant account', [
            'exception' => $exception,
        ]);
    }
}
