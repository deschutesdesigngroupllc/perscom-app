<?php

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewCombatRecordMail;
use App\Models\CombatRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewCombatRecord extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected CombatRecord $combatRecord)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\CombatRecord::uriKey(),
            'resourceId' => $this->combatRecord->id,
        ]);
    }

    /**
     * @return array<mixed>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): NewCombatRecordMail
    {
        return (new NewCombatRecordMail($this->combatRecord, $this->url))->to($notifiable->email);
    }

    public function toNova(): NovaNotification
    {
        return (new NovaNotification())->message('A new combat record has been added to your personnel file.')
            ->action('View Record', URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
