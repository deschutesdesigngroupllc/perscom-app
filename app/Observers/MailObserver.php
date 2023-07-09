<?php

namespace App\Observers;

use App\Jobs\SendBulkMail;
use App\Models\Mail;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MailObserver
{
    /**
     * Handle the Mail "created" event.
     *
     * @return void
     */
    public function created(Mail $mail)
    {
        $connection = Request::isCentralRequest() ? 'central' : config('queue.default');

        $recipients = match (true) {
            Request::isCentralRequest() => Tenant::findMany($mail->recipients),
            default => User::findMany($mail->recipients)
        };

        if ($mail->send_now) {
            SendBulkMail::dispatch($recipients, $mail)->onConnection($connection);
        } else {
            SendBulkMail::dispatch($recipients, $mail)->delay(Carbon::parse($mail->send_at)->diffInSeconds(now()))->onConnection($connection);
        }
    }

    /**
     * Handle the Mail "updated" event.
     *
     * @return void
     */
    public function updated(Mail $mail)
    {
        //
    }

    /**
     * Handle the Mail "deleted" event.
     *
     * @return void
     */
    public function deleted(Mail $mail)
    {
        //
    }

    /**
     * Handle the Mail "restored" event.
     *
     * @return void
     */
    public function restored(Mail $mail)
    {
        //
    }

    /**
     * Handle the Mail "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Mail $mail)
    {
        //
    }
}
