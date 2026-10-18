<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\DiscordInteractionService;
use App\Services\DiscordService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;

class DiscordRegisterCommandsCommand extends Command
{
    protected $signature = 'perscom:discord-register-commands';

    protected $description = 'Register the PERSCOM slash commands with Discord.';

    /**
     * @throws ConnectionException
     */
    public function handle(): int
    {
        if (blank(config('services.discord.client_id')) || blank(config('services.discord.token'))) {
            $this->components->error('Set DISCORD_CLIENT_ID and DISCORD_BOT_TOKEN before registering commands.');

            return static::FAILURE;
        }

        if (! DiscordService::registerCommands(DiscordInteractionService::commands())) {
            $this->components->error('Discord rejected the slash commands. Check the logs for details.');

            return static::FAILURE;
        }

        $this->components->info('The Discord slash commands have been registered.');

        return static::SUCCESS;
    }
}
