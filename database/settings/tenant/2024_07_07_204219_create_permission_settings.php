<?php

declare(strict_types=1);

use App\Models\Role;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('permission.default_roles', Role::query()->where('name', 'User')->pluck('id')->toArray());
        $this->migrator->add('permission.default_permissions', []);
    }
};
