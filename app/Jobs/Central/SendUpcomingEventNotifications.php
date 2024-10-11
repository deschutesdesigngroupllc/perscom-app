<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\Tenant\UpcomingEvent;
use DateInterval;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SendUpcomingEventNotifications implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(public int $tenantKey)
    {
        $this->onConnection('central');
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(function () {
            // Chunk the events to even out the workload
            Event::chunk(100, function (Collection $events) {
                $events
                    // Reject events that do not have notifications enabled, channels/intervals set or registration is disabled
                    ->reject(fn (Event $event) => ! $event->notifications_enabled
                        || blank($event->notifications_interval)
                        || blank($event->notifications_channels)
                        || ! $event->registration_enabled)

                    // Make sure the event has a start or next occurrence, and it's not in the past
                    ->reject(function (Event $event) {
                        $start = $event->starts ?? $event->schedule->next_occurrence ?? null;

                        if (blank($start)) {
                            return true;
                        }

                        return $start->addMinute()->isPast();
                    })

                    // Group by the intervals, so we can each interval one at a time
                    ->groupBy(function (Event $event) {
                        return $event->notifications_interval;
                    })

                    // Map app the events to notifications by interval, and then flatten to remove the group by keys
                    ->flatMap(function (Collection $events, $interval) {
                        return $events
                            ->reject(function (Event $event) use ($interval) {
                                $start = $event->starts ?? $event->schedule->next_occurrence ?? null;

                                if (blank($start)) {
                                    return true;
                                }

                                $sendWhen = $start->copy()->subtract(new DateInterval(Str::upper($interval)));

                                if ($sendWhen->isSameMinute(now())) {
                                    return false;
                                }

                                return true;
                            })
                            ->map(fn (Event $event) => new UpcomingEvent($event, NotificationInterval::from($interval)));
                    })

                    // Dispatch the notifications
                    ->each(fn (UpcomingEvent $notification) => $notification->event->registrations->each(function (User $user) use ($notification) {
                        $user->notify($notification);
                    }));
            });
        });
    }
}
