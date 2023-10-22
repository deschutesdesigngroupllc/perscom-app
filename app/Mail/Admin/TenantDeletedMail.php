<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected string $tenant, protected string $email)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.admin.tenant.deleted')
            ->subject('Tenant Deleted')
            ->with([
                'organization' => $this->tenant,
                'email' => $this->email,
                'url' => route('nova.pages.index', [
                    'resource' => \App\Nova\Tenant::uriKey(),
                ]),
            ]);
    }
}
