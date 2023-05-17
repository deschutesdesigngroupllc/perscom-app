<?php

namespace App\Console\Commands;

use App\Jobs\ResetDemoAccount as ResetDemoAccountJob;
use Illuminate\Console\Command;

class ResetDemoAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perscom:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset\'s the demo account data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ResetDemoAccountJob::dispatch();

        $this->info('The job has been dispatched to the queue.');

        return Command::SUCCESS;
    }
}
