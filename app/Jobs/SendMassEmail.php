<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\System\MassEmail;
use App\Models\Mail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail as MailFacade;

class SendMassEmail implements ShouldQueue
{
    use Batchable, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public function __construct(protected Tenant|User $notifiable, protected Mail $mail)
    {
        if (! $this->mail->send_now && filled($this->mail->send_at)) {
            $this->delay(now()->diff($this->mail->send_at));
        }
    }

    public function handle(): void
    {
        MailFacade::to($this->notifiable)->send(new MassEmail($this->mail));

        $this->mail->update([
            'sent_at' => now(),
        ]);
    }
}
