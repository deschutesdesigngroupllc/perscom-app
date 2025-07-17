<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\Batches\SendTenantMassEmails;
use App\Models\Mail;
use Throwable;

class MailObserver
{
    /**
     * @throws Throwable
     */
    public function created(Mail $mail): void
    {
        SendTenantMassEmails::handle($mail);
    }

    /**
     * @throws Throwable
     */
    public function updated(Mail $mail): void
    {
        SendTenantMassEmails::handle($mail);
    }
}
