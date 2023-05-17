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

    /**
     * @var array|string|string[]
     */
    protected $url;

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
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', NovaChannel::class];
    }

    /**
     * @return NewCombatRecordMail
     */
    public function toMail($notifiable)
    {
        return (new NewCombatRecordMail($this->combatRecord, $this->url))->to($notifiable->email);
    }

    /**
     * Get the nova representation of the notification
     *
     * @return NovaNotification
     */
    public function toNova()
    {
        return (new NovaNotification())->message('A new combat record has been added to your personnel file.')
            ->action('View Record', URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
