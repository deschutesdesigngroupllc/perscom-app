<?php

namespace App\Mail\Admin;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTenantMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected Tenant $tenant)
    {
        //
    }

    public function build(): static
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
