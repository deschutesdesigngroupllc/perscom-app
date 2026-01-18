<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('onboarding.completed', false);
        $this->migrator->add('onboarding.dismissed', false);
        $this->migrator->add('onboarding.completed_at', null);
    }
};
