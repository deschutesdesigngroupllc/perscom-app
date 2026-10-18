<?php

declare(strict_types=1);

namespace App\Actions\Integrations;

use App\Models\Tenant;
use App\Services\DiscordService;
use App\Settings\IntegrationSettings;
use Illuminate\Support\Arr;

class DisconnectDiscordServer
{
    public static function handle(): void
    {
        /** @var IntegrationSettings $settings */
        $settings = resolve(IntegrationSettings::class);

        $guildId = data_get($settings->discord_settings, 'discord_server');

        /** @var ?Tenant $tenant */
        $tenant = tenant();

        if ($tenant instanceof Tenant && $tenant->discord_guild_id === $guildId) {
            $tenant->forceFill(['discord_guild_id' => null])->save();
        }

        if (filled($guildId)) {
            rescue(fn (): bool => DiscordService::leaveServer((string) $guildId), report: false);
        }

        $settings->discord_settings = [
            ...Arr::except($settings->discord_settings ?? [], ['discord_server', 'discord_server_name', 'discord_channel', 'managed_roles']),
            'discord_enabled' => false,
        ];
        $settings->save();
    }
}
