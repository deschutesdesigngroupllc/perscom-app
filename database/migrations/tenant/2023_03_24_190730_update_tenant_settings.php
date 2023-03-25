<?php

use App\Models\Role;
use App\Models\Settings;
use Illuminate\Database\Migrations\Migration;
use Outl1ne\NovaSettings\NovaSettings;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ($tenant = tenant()) {
            Settings::withoutEvents(static function () use ($tenant) {
                $settings = nova_get_settings();

                if (! isset($settings['organization'])) {
                    NovaSettings::setSettingValue('organization', $tenant->name);
                }
                if (! isset($settings['email'])) {
                    NovaSettings::setSettingValue('email', $tenant->email);
                }
                if (! isset($settings['timezone'])) {
                    NovaSettings::setSettingValue('timezone', config('app.timezone'));
                }
                if (! isset($settings['dashboard_title'])) {
                    NovaSettings::setSettingValue('dashboard_title', $tenant->name);
                }
                if (! isset($settings['registration_enabled'])) {
                    NovaSettings::setSettingValue('registration_enabled', true);
                }
                if (! isset($settings['default_roles'])) {
                    NovaSettings::setSettingValue('default_roles', Role::query()->where('name', 'User')->pluck('id')->toArray());
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
