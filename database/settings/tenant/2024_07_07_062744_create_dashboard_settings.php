<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dashboard.title', 'Tenant');
        $this->migrator->add('dashboard.subtitle');
        $this->migrator->add('dashboard.domain');
        $this->migrator->add('dashboard.subdomain');
        $this->migrator->add('dashboard.cover_photo_height', 100);
    }
};
