<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\DiscordInteractionService;
use App\Services\DiscordService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;

#[Description('Register the PERSCOM slash commands with Discord.')]
#[Signature('perscom:discord-register-commands')]
class DiscordRegisterCommandsCommand extends Command
{
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
