<?php

namespace App\Jobs;

use App\Models\Mail;
use App\Notifications\Tenant\NewMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class SendBulkMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Collection $recipients, protected Mail $mail)
    {
        //
    }

    public function handle(): void
    {
        $this->mail->update([
            'sent_at' => now(),
        ]);

        Notification::send($this->recipients, new NewMail($this->mail));
    }
}
