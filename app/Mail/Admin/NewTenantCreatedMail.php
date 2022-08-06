<?php

namespace App\Mail\Admin;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTenantCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected Tenant $tenant)
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
        return $this->markdown('emails.admin.tenant.new')
            ->subject('New Tenant Created')
            ->with([
                'organization' => $this->tenant->name,
                'email' => $this->tenant->email,
                'domain' => $this->tenant->url,
                'url' => route('nova.pages.detail', [
                    'resource' => 'tenants',
                    'resourceId' => $this->tenant->getTenantKey(),
                ]),
            ]);
    }
}
