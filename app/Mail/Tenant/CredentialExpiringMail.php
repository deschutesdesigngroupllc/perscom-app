<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CredentialExpiringMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $title,
        public string $name,
        public string $kind,
        public string $expiresAt,
        public int $daysUntilExpiry,
        public ?string $url = null,
    ) {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.credential-expiring')
            ->subject($this->title)
            ->with([
                'name' => $this->name,
                'kind' => $this->kind,
                'expiresAt' => $this->expiresAt,
                'daysUntilExpiry' => $this->daysUntilExpiry,
                'url' => $this->url,
            ]);
    }
}
