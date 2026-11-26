<?php

declare(strict_types=1);

namespace App\Mail\System;

use App\Jobs\System\RemoveInactiveAccounts;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use function sprintf;

class DeleteAccountOneWeek extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected string $organization;

    protected ?string $url;

    protected string $deletionDate;

    public function __construct(Tenant $tenant)
    {
        $this->organization = $tenant->name;
        $this->url = $tenant->url;
        $this->deletionDate = RemoveInactiveAccounts::deletionDate($tenant);
    }

    public function build(): static
    {
        return $this->markdown('emails.system.delete-account-one-week')
            ->subject(sprintf('%s will be deleted in 7 days', $this->organization))
            ->with([
                'organization' => $this->organization,
                'url' => $this->url,
                'deletionDate' => $this->deletionDate,
            ]);
    }
}
