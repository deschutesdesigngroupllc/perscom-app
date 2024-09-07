<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RunEnvoyerHeartbeat extends Command
{
    protected $signature = 'perscom:heartbeat';

    protected $description = 'Sends a heartbeat to Envoyer.';

    public function handle(): int
    {
        if ($url = config('services.envoyer.heartbeat_url')) {
            $response = Http::get($url);

            if ($response->ok()) {
                return static::SUCCESS;
            }

            $this->error('There was an error when attempting the heartbeat.');

            return static::FAILURE;
        }

        $this->error('No heartbeat URL found.');

        return static::SUCCESS;
    }
}
