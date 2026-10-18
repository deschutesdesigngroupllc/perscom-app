<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RequiresTenancy;
use App\Contracts\RunsPerTenant;
use App\Jobs\Concerns\RunsForTenant;
use App\Models\Tenant;
use App\Settings\IntegrationSettings;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Records the Discord server organizations picked before servers were linked through the install
 * flow, so slash commands work without reconnecting. A server already claimed by another
 * organization is left for that organization.
 */
class LinkDiscordServer implements RequiresTenancy, RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    public function __construct(public ?int $tenantKey = null)
    {
        $this->configureForTenancy();
    }

    public function work(): void
    {
        /** @var ?Tenant $tenant */
        $tenant = tenant();

        $guildId = data_get(resolve(IntegrationSettings::class)->discord_settings, 'discord_server');

        if (! $tenant instanceof Tenant || blank($guildId) || filled($tenant->discord_guild_id)) {
            return;
        }

        if (Tenant::query()->where('discord_guild_id', $guildId)->exists()) {
            return;
        }

        $tenant->forceFill(['discord_guild_id' => (string) $guildId])->save();
    }
}
