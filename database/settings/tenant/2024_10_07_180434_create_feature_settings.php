<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('feature.advanced_notifications', [
            'discord_enabled' => true,
            'discord_channel_id' => null,
            'sms_enabled' => true,
        ]);
    }
};
