<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('organization.name', 'PERSCOM');
        $this->migrator->add('organization.email', 'tenant@perscom.io');
        $this->migrator->add('organization.timezone', config('app.timezone'));
    }
};
