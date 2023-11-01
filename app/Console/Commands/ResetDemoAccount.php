<?php

namespace App\Console\Commands;

use App\Jobs\ResetDemoAccount as ResetDemoAccountJob;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ResetDemoAccount extends Command
{
    protected $signature = 'perscom:demo';

    protected $description = 'Reset\'s the demo account data.';

    public function handle(): int
    {
        ResetDemoAccountJob::dispatch();

        $this->info('The job has been dispatched to the queue.');

        return CommandAlias::SUCCESS;
    }
}
