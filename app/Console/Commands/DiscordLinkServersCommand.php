<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Tenancy\DispatchTenantJob;
use App\Jobs\Tenant\LinkDiscordServer;
use Illuminate\Console\Command;
use Throwable;

class DiscordLinkServersCommand extends Command
{
    protected $signature = 'perscom:discord-link-servers';

    protected $description = 'Link each organization to the Discord server it already selected so slash commands work.';

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
