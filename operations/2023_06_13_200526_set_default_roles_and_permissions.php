<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Settings\PermissionSettings;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant): void {
            /** @var PermissionSettings $settings */
            $settings = app(PermissionSettings::class);
            $settings->default_permissions = [];
            $settings->default_roles = ['User'];
            $settings->save();
        });
    }
};
