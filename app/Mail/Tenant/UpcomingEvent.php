<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\User;
use App\Services\UserSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpcomingEvent extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected string $title;

    protected string $date;

    protected string $time;

    public function __construct(protected Event $event, protected NotificationInterval $interval, protected User $user)
    {
        $timezone = UserSettingsService::get(
            key: 'timezone',
            default: config('app.timezone'),
            user: $this->user
        );

        $start = $this->event->starts->setTimezone($timezone)->shiftTimezone('UTC');

        $this->date = $start->toFormattedDayDateString();
        $this->time = $start->format('g:i A');

        $this->title = match ($this->interval) {
            NotificationInterval::PT0M => "{$this->event->name} - Now",
            NotificationInterval::PT15M => "{$this->event->name} - 15 Minutes",
            NotificationInterval::PT1H => "{$this->event->name} - 1 Hour",
            NotificationInterval::P1D => "{$this->event->name} - Tomorrow at $this->time",
            NotificationInterval::P1W => "{$this->event->name} - Next Week on $this->date at $this->time",
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Upcoming Event: $this->title",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.upcoming-event',
            with: [
                'name' => $this->event->name,
                'start' => "$this->date at $this->time",
                'time' => $this->event->starts->toDateTimeString(),
                'url' => $this->event->url,
            ]
        );
    }
}
