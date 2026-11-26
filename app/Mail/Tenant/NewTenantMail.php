<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use function sprintf;

class NewTenantMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected ?string $url;

    protected ?string $email;

    protected ?string $organization;

    public function __construct(Tenant $tenant, User $user, protected string $setPasswordUrl)
    {
        $this->url = $tenant->url;
        $this->email = $user->email;
        $this->organization = $tenant->name;
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.new')
            ->subject(sprintf('%s is ready on PERSCOM', $this->organization))
            ->with([
                'url' => $this->url,
                'email' => $this->email,
                'organization' => $this->organization,
                'setPasswordUrl' => $this->setPasswordUrl,
            ]);
    }
}
