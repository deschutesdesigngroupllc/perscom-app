<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\NewSubscriptionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Cashier\Subscription;

class NewSubscription extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected Subscription $subscription)
    {
        //
    }

    /**
     * @return string[]
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
