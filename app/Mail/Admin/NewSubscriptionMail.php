<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;

class NewSubscriptionMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected Subscription $subscription)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Subscription',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.new-subscription',
            with: [
                'tenant' => $this->subscription->owner->name, // @phpstan-ignore-line
                'url' => $this->subscription->owner->url, // @phpstan-ignore-line
                'plan' => $this->subscription->owner->sparkPlan()?->name, // @phpstan-ignore-line
                'interval' => Str::ucfirst($this->subscription->owner->sparkPlan()?->interval), // @phpstan-ignore-line
            ]
        );
    }
}
