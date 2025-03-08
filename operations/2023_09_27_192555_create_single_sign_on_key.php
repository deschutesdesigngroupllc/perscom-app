<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Settings\IntegrationSettings;
use Illuminate\Support\Str;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant): void {
            /** @var IntegrationSettings $settings */
            $settings = app(IntegrationSettings::class);
            $settings->single_sign_on_key = Str::random(40);
            $settings->save();
        });
    }
};
