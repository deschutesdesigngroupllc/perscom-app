<?php

declare(strict_types=1);

namespace App\Mail\User;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountApproved extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected string $url;

    public function __construct(protected Tenant $tenant)
    {
        $this->url = $tenant->url;
    }

    public function build(): static
    {
        return $this->markdown('emails.user.account-approved')
            ->subject('Account Approved')
            ->with([
                'url' => $this->url,
            ]);
    }
}
