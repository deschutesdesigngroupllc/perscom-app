<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\RemoveInactiveAccounts as RemoveInactiveAccountsJob;
use Illuminate\Console\Command;

class RemoveInactiveAccountsCommand extends Command
{
    protected $signature = 'perscom:inactive-accounts';

    protected $description = 'Remove any inactive accounts.';

    public function handle(): int
    {
        RemoveInactiveAccountsJob::dispatch();

        $this->info('The remove inactive accounts job has been dispatched to the queue.');

        return static::SUCCESS;
    }
}
