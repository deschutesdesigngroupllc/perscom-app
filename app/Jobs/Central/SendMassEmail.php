<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use App\Contracts\RequiresTenancy;
use App\Mail\System\MassEmail;
use App\Models\Mail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail as MailFacade;

class SendMassEmail implements RequiresTenancy, ShouldQueue
{
    use Batchable;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected bool $queuedToSendNow;

    protected ?int $queuedForTimestamp;

    public function __construct(protected Tenant|User $notifiable, protected Mail $mail)
    {
        $this->queuedToSendNow = $this->mail->send_now;
        $this->queuedForTimestamp = $this->mail->send_at?->getTimestamp();

        if (! $this->mail->send_now && filled($this->mail->send_at)) {
            $this->delay(now()->diff($this->mail->send_at));
        }

        $this->onConnection('central');
    }

    public function handle(): void
    {
        if ($this->wasRescheduled()) {
            return;
        }

        MailFacade::to($this->notifiable)->send(new MassEmail($this->mail));

        $this->mail->update([
            'sent_at' => now(),
        ]);
    }

    /**
     * Determine whether the mail was rescheduled after this job was queued.
     *
     * Rescheduling queues a fresh batch for the new time, so a job queued for the old one sends
     * nothing.
     */
    protected function wasRescheduled(): bool
    {
        return $this->mail->send_now !== $this->queuedToSendNow
            || $this->mail->send_at?->getTimestamp() !== $this->queuedForTimestamp;
    }
}
