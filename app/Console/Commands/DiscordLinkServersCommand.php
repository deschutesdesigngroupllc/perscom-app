<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\LinkDiscordServer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Description('Link each organization to the Discord server it already selected so slash commands work.')]
#[Signature('perscom:discord-link-servers')]
class DiscordLinkServersCommand extends Command
{
    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        DispatchTenantJob::for(LinkDiscordServer::class, name: 'Link Discord Servers');

        $this->components->info('The Discord server link jobs have been dispatched.');

        return static::SUCCESS;
    }
}
