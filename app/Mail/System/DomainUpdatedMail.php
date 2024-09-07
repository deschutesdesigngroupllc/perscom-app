<?php

declare(strict_types=1);

namespace App\Mail\System;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class DomainUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected Domain $domain)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.system.domain-updated')
            ->subject('Domain Successfully Updated')
            ->with([
                'url' => $url = $this->domain->url,
                'fallback_url' => $fallback = $this->domain->tenant->fallback_url,
                'show_fallback' => ! Str::is($url, $fallback),
            ]);
    }
}
