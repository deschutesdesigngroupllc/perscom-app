<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RunEnvoyerHeartbeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perscom:heartbeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a heartbeat to Envoyer.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($url = env('ENVOYER_HEARTBEAT_URL')) {
            $response = Http::get($url);

            if ($response->ok()) {
                return self::SUCCESS;
            }

            $this->error('There was an error when attempting the heartbeat.');

            return Command::FAILURE;
        }

        $this->info('No heartbeat URL found.');

        return self::SUCCESS;
    }
}
