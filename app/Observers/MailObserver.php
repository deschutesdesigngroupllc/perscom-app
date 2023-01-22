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
     *
     * @param  \App\Models\Mail  $mail
     * @return void
     */
    public function created(Mail $mail)
    {
        if ($mail->send_now) {
            SendBulkMail::dispatch(Tenant::all(), $mail);
        } else {
            SendBulkMail::dispatch(Tenant::all(), $mail)->delay(Carbon::parse($mail->send_at)->diffInSeconds(now()));
        }
    }

    /**
     * Handle the Mail "updated" event.
     *
     * @param  \App\Models\Mail  $mail
     * @return void
     */
    public function updated(Mail $mail)
    {
        //
    }

    /**
     * Handle the Mail "deleted" event.
     *
     * @param  \App\Models\Mail  $mail
     * @return void
     */
    public function deleted(Mail $mail)
    {
        //
    }

    /**
     * Handle the Mail "restored" event.
     *
     * @param  \App\Models\Mail  $mail
     * @return void
     */
    public function restored(Mail $mail)
    {
        //
    }

    /**
     * Handle the Mail "force deleted" event.
     *
     * @param  \App\Models\Mail  $mail
     * @return void
     */
    public function forceDeleted(Mail $mail)
    {
        //
    }
}
