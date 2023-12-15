<?php

namespace App\Console\Commands;

use App\Jobs\ResetDemoAccount as ResetDemoAccountJob;
use Database\Seeders\FireServiceSeeder;
use Database\Seeders\MilitarySeeder;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ResetDemoAccount extends Command
{
    protected $signature = 'perscom:demo {--seeder=military}';

    protected $description = 'Reset\'s the demo account data.';

    public function handle(): int
    {
        $seeder = match (true) {
            $this->option('seeder') === 'fire' => FireServiceSeeder::class,
            default => MilitarySeeder::class
        };

        ResetDemoAccountJob::dispatch($seeder);

        $this->info('The job has been dispatched to the queue.');

        return CommandAlias::SUCCESS;
    }
}
