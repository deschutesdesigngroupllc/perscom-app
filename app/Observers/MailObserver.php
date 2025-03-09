<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\Batches\SendMassEmail;
use App\Models\Mail;
use Throwable;

class MailObserver
{
    /**
     * @throws Throwable
     */
    public function created(Mail $mail): void
    {
        SendMassEmail::handle($mail);
    }
}
