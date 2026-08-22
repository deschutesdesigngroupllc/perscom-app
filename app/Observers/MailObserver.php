<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\Tenancy\DispatchTenantMassEmails;
use App\Models\Mail;
use Throwable;

class MailObserver
{
    /**
     * @throws Throwable
     */
    public function created(Mail $mail): void
    {
        DispatchTenantMassEmails::handle($mail);
    }

    /**
     * @throws Throwable
     */
    public function updated(Mail $mail): void
    {
        DispatchTenantMassEmails::handle($mail);
    }
}
