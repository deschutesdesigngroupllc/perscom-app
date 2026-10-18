<?php

declare(strict_types=1);

namespace App\Actions\Integrations;

use App\Services\SlackService;
use App\Settings\IntegrationSettings;

class DisconnectSlackWorkspace
{
    public static function handle(): void
    {
        rescue(fn () => SlackService::revoke(), report: false);

        /** @var IntegrationSettings $settings */
        $settings = resolve(IntegrationSettings::class);
        $settings->slack_settings = null;
        $settings->save();
    }
}
