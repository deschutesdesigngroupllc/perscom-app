<?php

declare(strict_types=1);

use App\Models\Enums\RosterMode;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('dashboard.roster_mode', RosterMode::AUTOMATIC);
    }
};
