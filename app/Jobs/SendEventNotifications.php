<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Notifications\User\UpcomingEvent;
use DateInterval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SendEventNotifications implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Chunk the events to even out the workload
        Event::chunk(100, function (Collection $events) {
            $events
                // Reject any that do not have notifications enabled or no interval set
                ->reject(fn (Event $event) => ! $event->notifications_enabled || blank($event->notifications_interval))

                // Make sure the event has a next occurrence
                ->filter(fn (Event $event) => $event->next_occurrence)

                // Group by the intervals, so we can each interval one at a time
                ->groupBy(function (Event $event) {
                    return $event->notifications_interval;
                })

                // Map app the events to notifications by interval, and then flatten to remove the group by keys
                ->flatMap(function (Collection $events, $interval) {
                    return $events
                        ->reject(function (Event $event) use ($interval) {
                            $sendWhen = $event->next_occurrence->copy()->subtract(new DateInterval(Str::upper($interval)));

                            if ($sendWhen->isSameMinute(now())) {
                                return false;
                            }

                            return true;
                        })
                        ->map(fn (Event $event) => new UpcomingEvent($event, NotificationInterval::from($interval)));
                })

                // Dispatch the notifications
                ->each(fn (UpcomingEvent $notification) => dispatch($notification));
        });
    }
}
