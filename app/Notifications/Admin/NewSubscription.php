<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Mail\Admin\NewSubscriptionMail;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewSubscription extends Notification implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(protected Subscription $subscription)
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): NewSubscriptionMail
    {
        return (new NewSubscriptionMail($this->subscription))->to($notifiable->email);
    }
}
