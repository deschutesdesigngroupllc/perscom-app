<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Actions\Tenant\RemoveTenantAccount as RemoveTenantAccountAction;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class RemoveTenantAccount implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Tenant $tenant)
    {
        //
    }

    public function handle(): void
    {
        /** @var RemoveTenantAccountAction $action */
        $action = resolve(RemoveTenantAccountAction::class);
        $action->handle($this->tenant);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Failed to remove tenant account', [
            'exception' => $exception,
        ]);
    }
}
