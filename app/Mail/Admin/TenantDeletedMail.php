<?php

declare(strict_types=1);

namespace App\Mail\Admin;

use App\Filament\Admin\Resources\TenantResource;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantDeletedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

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
                'url' => TenantResource::getUrl(panel: 'admin'),
            ]);
    }
}
