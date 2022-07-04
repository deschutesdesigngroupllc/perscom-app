<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Laravel\Cashier\Subscription;
use Laravel\Nova\Notifications\NovaChannel;

class NewSubscription extends Notification
{
    use Queueable;

    protected $subscription;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [NovaChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

	/**
	 * Get the nova representation of the notification
	 *
	 * @return array
	 */
	public function toNova()
	{
		return (new NovaNotification)
			->message('Your report is ready to download.')
			->action('Download', URL::remote('https://example.com/report.pdf'))
			->icon('download')
			->type('info');
	}
}
