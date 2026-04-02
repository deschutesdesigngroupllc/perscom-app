<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Settings\OnboardingSettings;
use Illuminate\Support\Facades\Date;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant): void {
            /** @var OnboardingSettings $settings */
            $settings = resolve(OnboardingSettings::class);

            $settings->completed = true;
            $settings->completed_at = Date::now()->toIso8601String();
            $settings->save();
        });
    }
};
