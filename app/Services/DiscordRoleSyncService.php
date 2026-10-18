<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Settings\IntegrationSettings;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Keeps a member's Discord roles and nickname in step with their PERSCOM rank and unit. PERSCOM
 * creates one role per rank and per unit and only ever adds or removes the roles it created.
 */
class DiscordRoleSyncService
{
    public static function isEnabled(): bool
    {
        $settings = DiscordRoleSyncService::settings();

        return filled(data_get($settings, 'discord_server'))
            && (filled(data_get($settings, 'sync_roles')) || filled(data_get($settings, 'sync_nicknames')));
    }

    /**
     * @throws ConnectionException
     */
    public static function sync(User $user): void
    {
        $settings = DiscordRoleSyncService::settings();
        $guildId = (string) data_get($settings, 'discord_server');

        if (blank($guildId) || blank($user->discord_user_id)) {
            return;
        }

        $member = DiscordService::getMember($guildId, (string) $user->discord_user_id);

        if ($member === null) {
            return;
        }

        if (filled(data_get($settings, 'sync_roles'))) {
            DiscordRoleSyncService::syncRoles($user, $guildId, (array) data_get($member, 'roles', []));
        }

        if (filled(data_get($settings, 'sync_nicknames'))) {
            DiscordService::setNickname($guildId, (string) $user->discord_user_id, DiscordRoleSyncService::nickname($user));
        }
    }

    public static function nickname(User $user): string
    {
        return trim(sprintf('%s %s', $user->rank?->abbreviation, $user->name));
    }

    /**
     * @param  array<int, string>  $currentRoleIds
     *
     * @throws ConnectionException
     */
    protected static function syncRoles(User $user, string $guildId, array $currentRoleIds): void
    {
        $user->loadMissing(['rank', 'unit']);

        $desired = DiscordRoleSyncService::desiredRoles($user)
            ->map(fn (string $name, string $key): ?string => DiscordRoleSyncService::ensureRole($guildId, $key, $name))
            ->filter()
            ->values();

        $managed = collect((array) data_get(DiscordRoleSyncService::settings(), 'managed_roles', []))->values();
        $current = collect($currentRoleIds);

        $desired->diff($current)->each(fn (string $roleId): bool => DiscordService::addMemberRole($guildId, (string) $user->discord_user_id, $roleId));

        $current->intersect($managed)->diff($desired)->each(fn (string $roleId): bool => DiscordService::removeMemberRole($guildId, (string) $user->discord_user_id, $roleId));
    }

    /**
     * @return Collection<string, string>
     */
    protected static function desiredRoles(User $user): Collection
    {
        return collect([
            'rank:'.$user->rank_id => $user->rank?->name,
            'unit:'.$user->unit_id => $user->unit?->name,
        ])->filter(fn (?string $name): bool => filled($name));
    }

    /**
     * Finds the role PERSCOM created for a rank or unit, recreating it if someone deleted it in
     * Discord. A lock stops two members being synced at once from creating duplicate roles.
     *
     * @throws ConnectionException
     */
    protected static function ensureRole(string $guildId, string $key, string $name): ?string
    {
        return Cache::lock(sprintf('discord-role:%s:%s', $guildId, $key), 30)->block(15, function () use ($guildId, $key, $name): ?string {
            $roleId = data_get(DiscordRoleSyncService::settings(fresh: true), 'managed_roles.'.$key);

            $exists = filled($roleId) && collect(DiscordService::getRoles($guildId))->contains(fn (array $role): bool => (string) data_get($role, 'id') === (string) $roleId);

            if ($exists) {
                return (string) $roleId;
            }

            $roleId = DiscordService::createRole($guildId, $name);

            if (filled($roleId)) {
                /** @var IntegrationSettings $settings */
                $settings = resolve(IntegrationSettings::class);
                $settings->refresh();
                $settings->discord_settings = [
                    ...($settings->discord_settings ?? []),
                    'managed_roles' => [...(array) data_get($settings->discord_settings, 'managed_roles', []), $key => $roleId],
                ];
                $settings->save();
            }

            return $roleId;
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected static function settings(bool $fresh = false): array
    {
        /** @var IntegrationSettings $settings */
        $settings = resolve(IntegrationSettings::class);

        if ($fresh) {
            $settings->refresh();
        }

        return $settings->discord_settings ?? [];
    }
}
