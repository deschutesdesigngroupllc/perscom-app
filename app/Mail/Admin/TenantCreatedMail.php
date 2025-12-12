<?php

declare(strict_types=1);

namespace App\Mail\Admin;

use App\Filament\Admin\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantCreatedMail extends Mailable implements ShouldQueue
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
            ->subject('A new tenant organization has been created')
            ->with([
                'organization' => $this->tenant->name,
                'email' => $this->tenant->email,
                'domain' => $this->tenant->url,
                'url' => TenantResource::getUrl('edit', [
                    'record' => $this->tenant,
                ], panel: 'admin'),
            ]);
    }
}
