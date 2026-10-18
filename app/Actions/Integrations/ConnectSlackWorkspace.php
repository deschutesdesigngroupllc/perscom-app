<?php

declare(strict_types=1);

namespace App\Actions\Integrations;

use App\Settings\IntegrationSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

class ConnectSlackWorkspace
{
    public static function handle(string $teamId, string $teamName, string $botToken): void
    {
        /** @var IntegrationSettings $settings */
        $settings = resolve(IntegrationSettings::class);

        $current = $settings->slack_settings ?? [];
        $changedWorkspace = data_get($current, 'team_id') !== $teamId;

        $settings->slack_settings = [
            ...($changedWorkspace ? Arr::except($current, ['slack_channel']) : $current),
            'slack_enabled' => true,
            'team_id' => $teamId,
            'team_name' => $teamName,
            'bot_token' => Crypt::encryptString($botToken),
        ];
        $settings->save();
    }
}
