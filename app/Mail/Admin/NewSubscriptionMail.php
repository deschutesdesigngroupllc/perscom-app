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
                'tenant' => $this->subscription->owner->name,
                'url' => $this->subscription->owner->url,
                'plan' => $this->subscription->owner->sparkPlan()?->name,
                'interval' => Str::ucfirst($this->subscription->owner->sparkPlan()?->interval),
            ]
        );
    }
}
