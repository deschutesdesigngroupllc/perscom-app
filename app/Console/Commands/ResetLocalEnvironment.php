<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ResetLocalEnvironment extends Command
{
    protected $signature = 'perscom:local';

    protected $description = 'Reset the local development environment.';

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('You may only run this command in the local environment.');

            return CommandAlias::FAILURE;
        }

        $this->call('tenants:migrate-fresh');
        $this->call('tenants:seed');
        $this->call('tenants:seed', [
            '--class' => DatabaseSeeder::class,
        ]);

        return CommandAlias::SUCCESS;
    }
}
