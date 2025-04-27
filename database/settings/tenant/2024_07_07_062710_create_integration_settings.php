<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('integration.single_sign_on_key', Str::random(40));
        $this->migrator->add('integration.discord_settings');
        $this->migrator->add('integration.sms_settings');
    }
};
