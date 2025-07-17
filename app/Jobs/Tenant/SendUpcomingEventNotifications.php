<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Actions\Events\SendUpcomingEventNotification;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\Tenant;
use DateInterval;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class SendUpcomingEventNotifications implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public int $tenantKey)
    {
        $this->onConnection('central');
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(function (): void {
            // Find all events where they are repeating
            Event::query()->with('schedule')->where('repeats', true)->chunk(100, function (Collection $events): void {
                $events
                    // Filter events that we can't send notifications for
                    ->filter(fn (Event $event): bool => SendUpcomingEventNotification::canSendNotification($event))

                    // Group by the intervals, so we can iterate over one interval at a time
                    ->groupBy('notifications_interval')

                    // Iterate over the collection of events for each interval
                    ->each(fn (Collection $events, $interval) => $events
                        // Iterate over all the events for a given interval
                        ->each(function (Event $event) use ($interval): void {
                            $start = $event->schedule->next_occurrence ?? $event->starts ?? null;

                            if (blank($start)) {
                                return;
                            }

                            // Subtract the interval to determine when the notification should be sent so that it
                            // hits the users at the correct interval time
                            $sendAt = $start->copy()->subtract(new DateInterval(Str::upper($interval)));

                            // If the time to send the notification is within the next 24 hours, sent it. We check
                            // 24 hours because the schedule that runs this job only happens once a day.
                            if ($sendAt->isBetween(now(), now()->addHours(24))) {
                                SendUpcomingEventNotification::handle($event, NotificationInterval::from($interval), $sendAt);
                            }
                        }));
            });
        });
    }
}
