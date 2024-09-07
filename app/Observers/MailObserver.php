<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SendBulkMail;
use App\Models\Mail;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class MailObserver
{
    public function created(Mail $mail): void
    {
        $connection = App::isAdmin() || App::runningInConsole() ? 'central' : config('queue.default');

        $recipients = match (true) {
            App::isAdmin() || App::runningInConsole() => Tenant::findMany($mail->recipients),
            default => User::findMany($mail->recipients)
        };

        if ($mail->send_now) {
            SendBulkMail::dispatch($recipients, $mail)->onConnection($connection);
        } else {
            SendBulkMail::dispatch($recipients, $mail)->delay(Carbon::parse($mail->send_at)->diffInSeconds(now()))->onConnection($connection);
        }
    }
}
