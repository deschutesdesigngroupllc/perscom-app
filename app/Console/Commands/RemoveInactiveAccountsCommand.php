<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\System\RemoveInactiveAccounts;
use Illuminate\Console\Command;

class RemoveInactiveAccountsCommand extends Command
{
    protected $signature = 'perscom:inactive-accounts';

    protected $description = 'Remove any inactive accounts.';

    public function handle(): int
    {
        RemoveInactiveAccounts::dispatch();

        $this->components->info('The remove inactive accounts job has been dispatched to the queue.');

        return static::SUCCESS;
    }
}
