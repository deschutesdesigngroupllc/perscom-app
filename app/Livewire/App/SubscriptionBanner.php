<?php

declare(strict_types=1);

namespace App\Livewire\App;

use App\Features\BillingFeature;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Feature;
use Livewire\Component;

class SubscriptionBanner extends Component
{
    public ?string $message;

    public bool $show = false;

    public function mount(): void
    {
        $date = tenant('trial_ends_at');

        $left = Carbon::parse($date)->longRelativeDiffForHumans();
        $expiration = Carbon::parse($date)->toFormattedDateString();

        $this->message = match (true) {
            tenant()?->onTrial() => "You are currently on trial. Your trial is set to expire $left on $expiration.",
            ! tenant()?->subscribed() => 'You do not currently have an active subscription. Please sign up for a subscription to continue using PERSCOM.',
            default => null
        };

        $this->show = filled($this->message) && Auth::user() && Feature::active(BillingFeature::class);
    }

    public function render(): View
    {
        return view('livewire.app.subscription-banner');
    }
}
