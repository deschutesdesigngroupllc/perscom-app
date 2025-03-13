<?php

declare(strict_types=1);

namespace App\Livewire\App;

use App\Models\Tenant;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class SubscriptionBanner extends Component
{
    public ?string $message = null;

    public bool $show = false;

    public function mount(): void
    {
        /** @var Tenant $tenant */
        $tenant = Filament::getTenant();

        $timezone = UserSettingsService::get('timezone', function () {
            /** @var OrganizationSettings $settings */
            $settings = app(OrganizationSettings::class);

            return $settings->timezone ?? config('app.timezone');
        });

        $trialEndsAt = $tenant->trial_ends_at;

        $left = null;
        $expiration = null;
        if (filled($trialEndsAt)) {
            $trialEndsAt->setTimezone($timezone)->shiftTimezone('UTC');
            $left = $trialEndsAt->longRelativeDiffForHumans();
            $expiration = $trialEndsAt->toFormattedDayDateString();
        }

        $this->message = match (true) {
            $tenant->onTrial() && isset($left, $expiration) => "You are currently on trial. Your trial is set to expire $left on $expiration.",
            $tenant->hasIncompletePayment() => 'Your subscription is currently past due. Please pay your invoice to continue using PERSCOM.',
            ! $tenant->subscribed() => 'You do not currently have an active subscription. Please sign up for a subscription to continue using PERSCOM.',
            default => null
        };

        $this->show = filled($this->message) && Auth::user() && Gate::check('billing');
    }

    public function render(): View
    {
        return view('livewire.app.subscription-banner');
    }
}
