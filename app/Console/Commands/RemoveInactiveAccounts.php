<?php

namespace App\Console\Commands;

use App\Jobs\RemoveInactiveAccounts as RemoveInactiveAccountsJob;
use Illuminate\Console\Command;

class RemoveInactiveAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perscom:inactive-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove any inactive accounts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        RemoveInactiveAccountsJob::dispatch();

        $this->info('The job has been dispatched to the queue.');

        return Command::SUCCESS;
    }
}
