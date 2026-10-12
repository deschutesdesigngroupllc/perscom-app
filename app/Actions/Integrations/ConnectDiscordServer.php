<?php

declare(strict_types=1);

namespace App\Actions\Integrations;

use App\Exceptions\IntegrationConnectionException;
use App\Models\Tenant;
use App\Settings\IntegrationSettings;
use Illuminate\Support\Arr;

class ConnectDiscordServer
{
    /**
     * Links a Discord server to the current organization. The server lookup for slash commands is
     * central, so a server can belong to only one organization at a time.
     *
     * @throws IntegrationConnectionException
     */
    public static function handle(string $guildId, string $guildName): void
    {
        /** @var ?Tenant $tenant */
        $tenant = tenant();

        if ($tenant instanceof Tenant) {
            $claimed = Tenant::query()
                ->where('discord_guild_id', $guildId)
                ->whereKeyNot($tenant->getKey())
                ->exists();

            if ($claimed) {
                throw IntegrationConnectionException::discordServerAlreadyConnected();
            }

            $tenant->forceFill(['discord_guild_id' => $guildId])->save();
        }

        /** @var IntegrationSettings $settings */
        $settings = resolve(IntegrationSettings::class);

        $current = $settings->discord_settings ?? [];
        $changedServer = data_get($current, 'discord_server') !== $guildId;

        $settings->discord_settings = [
            ...($changedServer ? Arr::except($current, ['discord_channel', 'managed_roles']) : $current),
            'discord_enabled' => true,
            'discord_server' => $guildId,
            'discord_server_name' => $guildName,
        ];
        $settings->save();
    }
}
