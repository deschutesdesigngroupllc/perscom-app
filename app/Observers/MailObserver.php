<?php

namespace App\Observers;

use App\Jobs\SendBulkMail;
use App\Models\Mail;
use App\Models\Tenant;
use Carbon\Carbon;

class MailObserver
{
    /**
     * Handle the Mail "created" event.
     */
    public function created(Mail $mail): void
    {
        if ($mail->send_now) {
            SendBulkMail::dispatch(Tenant::all(), $mail);
        } else {
            SendBulkMail::dispatch(Tenant::all(), $mail)->delay(Carbon::parse($mail->send_at)->diffInSeconds(now()));
        }
    }

    /**
     * Handle the Mail "updated" event.
     */
    public function updated(Mail $mail): void
    {
        //
    }

    /**
     * Handle the Mail "deleted" event.
     */
    public function deleted(Mail $mail): void
    {
        //
    }

    /**
     * Handle the Mail "restored" event.
     */
    public function restored(Mail $mail): void
    {
        //
    }

    /**
     * Handle the Mail "force deleted" event.
     */
    public function forceDeleted(Mail $mail): void
    {
        //
    }
}
