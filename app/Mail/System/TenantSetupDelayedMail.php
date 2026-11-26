<?php

declare(strict_types=1);

namespace App\Mail\System;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use function sprintf;

class TenantSetupDelayedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected Tenant $tenant)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.system.setup-delayed')
            ->subject(sprintf('%s is taking longer to set up', $this->tenant->name))
            ->with([
                'organization' => $this->tenant->name,
            ]);
    }
}
