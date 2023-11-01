<?php

namespace App\Console\Commands;

use App\Jobs\RemoveInactiveAccounts as RemoveInactiveAccountsJob;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class RemoveInactiveAccounts extends Command
{
    protected $signature = 'perscom:inactive-accounts';

    protected $description = 'Remove any inactive accounts.';

    public function handle(): int
    {
        RemoveInactiveAccountsJob::dispatch();

        $this->info('The job has been dispatched to the queue.');

        return CommandAlias::SUCCESS;
    }
}
