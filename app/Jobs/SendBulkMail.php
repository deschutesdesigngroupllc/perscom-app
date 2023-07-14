<?php

namespace App\Jobs;

use App\Models\Mail;
use App\Notifications\Tenant\NewMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Stancl\Tenancy\Database\TenantCollection;

class SendBulkMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected TenantCollection $tenants, protected Mail $mail)
    {
        //
    }

    public function handle(): void
    {
        $this->mail->update([
            'sent_at' => now(),
        ]);

        Notification::send($this->tenants, new NewMail($this->mail));
    }
}
