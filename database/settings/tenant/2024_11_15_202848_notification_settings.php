<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notifications.assignment_records');
        $this->migrator->add('notifications.award_records');
        $this->migrator->add('notifications.combat_records');
        $this->migrator->add('notifications.qualification_records');
        $this->migrator->add('notifications.rank_records');
        $this->migrator->add('notifications.service_records');
    }
};
