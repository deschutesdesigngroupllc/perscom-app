<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('permission.require_multi_factor_authentication', false);
    }

    public function down(): void
    {
        $this->migrator->delete('permission.require_multi_factor_authentication');
    }
};
