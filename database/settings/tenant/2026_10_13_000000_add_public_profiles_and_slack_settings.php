<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dashboard.public_profiles_enabled', false);
        $this->migrator->add('integration.slack_settings');
    }

    public function down(): void
    {
        $this->migrator->delete('dashboard.public_profiles_enabled');
        $this->migrator->delete('integration.slack_settings');
    }
};
