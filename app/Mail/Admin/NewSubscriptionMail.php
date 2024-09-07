<?php

declare(strict_types=1);

namespace App\Mail\Admin;

use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

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
        /** @var Tenant $owner */
        $owner = $this->subscription->owner->fresh();

        return new Content(
            markdown: 'emails.admin.new-subscription',
            with: [
                'tenant' => $owner->name,
                'url' => $owner->url,
                'plan' => $owner->sparkPlan()?->name,
                'interval' => Str::ucfirst($owner->sparkPlan()?->interval),
            ]
        );
    }
}
