<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Command\Command as CommandAlias;

class RunEnvoyerHeartbeat extends Command
{
    protected $signature = 'perscom:heartbeat';

    protected $description = 'Sends a heartbeat to Envoyer.';

    public function handle(): int
    {
        if ($url = env('ENVOYER_HEARTBEAT_URL')) {
            $response = Http::get($url);

            if ($response->ok()) {
                return CommandAlias::SUCCESS;
            }

            $this->error('There was an error when attempting the heartbeat.');

            return CommandAlias::FAILURE;
        }

        $this->info('No heartbeat URL found.');

        return CommandAlias::SUCCESS;
    }
}
