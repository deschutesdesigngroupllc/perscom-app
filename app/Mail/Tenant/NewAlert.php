<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAlert extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected Alert $alert)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.new-alert')
            ->subject($this->alert->title)
            ->with([
                'title' => $this->alert->title,
                'message' => $this->alert->message,
                'link' => $this->alert->link_text,
                'url' => $this->alert->url,
            ]);
    }
}
