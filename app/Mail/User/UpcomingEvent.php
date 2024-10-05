<?php

declare(strict_types=1);

namespace App\Mail\User;

use App\Models\Enums\NotificationInterval;
use App\Models\Event;
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

    public function __construct(protected Event $event, protected NotificationInterval $interval)
    {
        $date = $this->event->start->toFormattedDayDateString();
        $time = $this->event->start->format('g:i A');

        $this->title = match ($this->interval) {
            NotificationInterval::PT0M => "{$this->event->name} - Now",
            NotificationInterval::PT15M => "{$this->event->name} - 15 Minutes",
            NotificationInterval::PT1H => "{$this->event->name} - 1 Hour",
            NotificationInterval::P1D => "{$this->event->name} - Tomorrow at $time",
            NotificationInterval::P1W => "{$this->event->name} - Next Week on $date at $time",
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
        $date = $this->event->start->toFormattedDayDateString();
        $time = $this->event->start->format('g:i A');

        $title = match ($this->interval) {
            NotificationInterval::PT0M => "{$this->event->name} is starting now",
            NotificationInterval::PT15M => "{$this->event->name} is starting in 15 minutes",
            NotificationInterval::PT1H => "{$this->event->name} is starting in 1 hour",
            NotificationInterval::P1D => "{$this->event->name} begins tomorrow at $time",
            NotificationInterval::P1W => "{$this->event->name} begins next week on $date at $time",
        };

        return new Content(
            markdown: 'emails.user.upcoming-event',
            with: [
                'title' => $title,
                'name' => $this->event->name,
                'start' => "$date at $time",
                'time' => $this->event->start->toDateTimeString(),
                'url' => $this->event->url,
            ]
        );
    }
}
