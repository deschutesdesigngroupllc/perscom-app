<?php

declare(strict_types=1);

namespace App\Jobs\System;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteUnverifiedRegistrations implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $threeDaysAgo = now()->subDays(3);

        $deletedCount = Registration::query()
            ->whereNull('verified_at')
            ->where('created_at', '<=', $threeDaysAgo)
            ->delete();

        if ($deletedCount > 0) {
            Log::debug(sprintf('Deleted %d unverified registration(s) older than 3 days.', $deletedCount));
        }
    }
}
