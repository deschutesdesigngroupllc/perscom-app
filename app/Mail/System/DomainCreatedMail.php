<?php

namespace App\Mail\System;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

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
                'url' => $url = $this->domain->url,
                'fallback_url' => $fallback = $this->domain->tenant->fallback_url,
                'show_fallback' => ! Str::is($url, $fallback),
            ]);
    }
}
