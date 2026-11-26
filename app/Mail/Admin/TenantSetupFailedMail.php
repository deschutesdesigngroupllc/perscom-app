<?php

declare(strict_types=1);

namespace App\Mail\Admin;

use App\Filament\Admin\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use function sprintf;

class TenantSetupFailedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected Tenant $tenant, protected string $error)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.admin.tenant.setup-failed')
            ->subject(sprintf('Setup failed for %s', $this->tenant->name))
            ->with([
                'organization' => $this->tenant->name,
                'email' => $this->tenant->email,
                'error' => $this->error,
                'url' => TenantResource::getUrl('edit', ['record' => $this->tenant], panel: 'admin'),
            ]);
    }
}
