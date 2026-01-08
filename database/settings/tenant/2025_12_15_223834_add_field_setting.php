<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('fields.assignment_records');
        $this->migrator->add('fields.award_records');
        $this->migrator->add('fields.combat_records');
        $this->migrator->add('fields.qualification_records');
        $this->migrator->add('fields.rank_records');
        $this->migrator->add('fields.service_records');
        $this->migrator->add('fields.training_records');
    }
};
