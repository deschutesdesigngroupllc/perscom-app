<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('registration.enabled', true);
        $this->migrator->add('registration.admin_approval_required', false);
    }
};
