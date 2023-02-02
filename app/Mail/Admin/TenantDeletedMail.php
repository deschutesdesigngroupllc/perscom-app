<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected string $tenant, protected string $email)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.admin.tenant.deleted')->subject('Tenant Deleted')->with([
            'organization' => $this->tenant,
            'email' => $this->email,
            'url' => route('nova.pages.index', [
                'resource' => \App\Nova\Tenant::uriKey(),
            ]),
        ]);
    }
}
