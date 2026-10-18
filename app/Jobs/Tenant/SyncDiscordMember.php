<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Models\User;
use App\Services\DiscordRoleSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;

class SyncDiscordMember implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public int $userId)
    {
        $this->afterCommit = true;
    }

    /**
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $user = User::query()->find($this->userId);

        if (! $user instanceof User || ! DiscordRoleSyncService::isEnabled()) {
            return;
        }

        DiscordRoleSyncService::sync($user);
    }
}
