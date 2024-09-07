<?php

declare(strict_types=1);

use App\Models\Settings;
use App\Models\Tenant;
use App\Settings\DashboardSettings;
use App\Settings\IntegrationSettings;
use App\Settings\OrganizationSettings;
use App\Settings\PermissionSettings;
use App\Settings\RegistrationSettings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant) {
            if ($tenant->getKey() === 1) {
                return;
            }

            Artisan::call('tenants:migrate', [
                '--tenants' => [$tenant->getTenantKey()],
                '--path' => database_path('settings/tenant'),
                '--force' => true,
            ]);

            /** @var DashboardSettings $dashboardSettings */
            $dashboardSettings = app(DashboardSettings::class);
            $dashboardSettings->title = Settings::query()->where('name', 'dashboard_title')->pluck('value')->first() ?? $tenant->name;
            $dashboardSettings->subtitle = Settings::query()->where('name', 'dashboard_subtitle')->pluck('value')->first() ?? null;
            $dashboardSettings->subdomain = Settings::query()->where('name', 'subdomain')->pluck('value')->first() ?? null;
            $dashboardSettings->domain = null;
            $dashboardSettings->cover_photo_height = 100;
            $dashboardSettings->save();

            /** @var IntegrationSettings $integrationSettings */
            $integrationSettings = app(IntegrationSettings::class);
            $integrationSettings->single_sign_on_key = Settings::query()->where('name', 'single_sign_on_key')->pluck('value')->first() ?? Str::random(40);
            $integrationSettings->save();

            /** @var OrganizationSettings $organizationSettings */
            $organizationSettings = app(OrganizationSettings::class);
            $organizationSettings->name = Settings::query()->where('name', 'organization')->pluck('value')->first() ?? $tenant->name;
            $organizationSettings->email = Settings::query()->where('name', 'email')->pluck('value')->first() ?? $tenant->email;
            $organizationSettings->timezone = Settings::query()->where('name', 'timezone')->pluck('value')->first() ?? config('app.timezone');
            $organizationSettings->save();

            /** @var PermissionSettings $permissionSettings */
            $permissionSettings = app(PermissionSettings::class);
            $permissionSettings->default_permissions = json_decode(Settings::query()->where('name', 'default_permissions')->pluck('value')->first(default: '[]'), true) ?? [];
            $permissionSettings->default_roles = json_decode(Settings::query()->where('name', 'default_roles')->pluck('value')->first(default: "['User']"), true) ?? ['User'];
            $permissionSettings->save();

            /** @var RegistrationSettings $registrationSettings */
            $registrationSettings = app(RegistrationSettings::class);
            $registrationSettings->enabled = (bool) Settings::query()->where('name', 'registration_enabled')->pluck('value')->first() ?? true;
            $registrationSettings->admin_approval_required = (bool) Settings::query()->where('name', 'registration_admin_approval_required')->pluck('value')->first() ?? false;
            $registrationSettings->save();

            DB::statement('ALTER TABLE `settings` DROP COLUMN `value`');
            DB::statement('DELETE FROM `settings` WHERE `group` IS NULL');
        });
    }
};
