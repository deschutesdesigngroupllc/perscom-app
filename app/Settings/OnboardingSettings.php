<?php

declare(strict_types=1);

namespace App\Settings;

use Illuminate\Support\Carbon;
use Spatie\LaravelSettings\Settings;

class OnboardingSettings extends Settings
{
    public bool $completed;

    public bool $dismissed;

    public ?string $completed_at = null;

    public static function group(): string
    {
        return 'onboarding';
    }

    public function isAccessible(): bool
    {
        return ! $this->completed && ! $this->dismissed;
    }

    public function markCompleted(): void
    {
        $this->completed = true;
        $this->completed_at = Carbon::now()->toIso8601String();
        $this->save();
    }

    public function markDismissed(): void
    {
        $this->dismissed = true;
        $this->save();
    }
}
