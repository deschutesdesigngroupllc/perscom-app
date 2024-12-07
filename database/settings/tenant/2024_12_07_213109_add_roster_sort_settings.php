<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dashboard.roster_sort_order', [
            'ranks.order',
            'positions.order',
            'specialties.order',
            'users.name',
        ]);
    }
};
