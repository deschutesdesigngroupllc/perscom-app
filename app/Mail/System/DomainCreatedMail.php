<?php

namespace App\Mail\System;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DomainCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected Domain $domain)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.system.domain-created')
            ->subject('Domain Successfully Created')
            ->with([
                'url' => $this->domain->url,
                'fallback_url' => $this->domain->tenant->fallback_url,
            ]);
    }
}
