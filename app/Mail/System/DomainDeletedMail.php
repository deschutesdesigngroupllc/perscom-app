<?php

declare(strict_types=1);

namespace App\Mail\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DomainDeletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected string $domain, protected string $url)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.system.domain-deleted')
            ->subject('Domain Removed')
            ->with([
                'url' => $this->url,
                'removed_url' => $this->domain,
            ]);
    }
}
