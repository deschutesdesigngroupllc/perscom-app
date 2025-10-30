<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Actions\Tenant\SetupTenantAccount as SetupTenantAccountAction;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SetupTenantAccount implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Tenant $tenant)
    {
        //
    }

    public function handle(): void
    {
        /** @var SetupTenantAccountAction $action */
        $action = app(SetupTenantAccountAction::class);
        $action->handle($this->tenant);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Failed to setup tenant account', [
            'exception' => $exception,
        ]);
    }
}
