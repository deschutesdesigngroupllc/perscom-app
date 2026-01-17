<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Settings\OnboardingSettings;
use Illuminate\Support\Carbon;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant): void {
            /** @var OnboardingSettings $settings */
            $settings = app(OnboardingSettings::class);

            $settings->completed = true;
            $settings->completed_at = Carbon::now()->toIso8601String();
            $settings->save();
        });
    }
};
