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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected Subscription $subscription)
    {
        //
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(subject: 'New Subscription');
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(markdown: 'emails.admin.new-subscription', with: [
            'tenant' => $this->subscription->owner->name,
            'url' => $this->subscription->owner->url,
            'plan' => $this->subscription->owner->sparkPlan()->name,
            'interval' => Str::ucfirst($this->subscription->owner->sparkPlan()->interval),
        ]);
    }
}
