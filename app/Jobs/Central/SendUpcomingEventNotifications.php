<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use App\Actions\Events\SendUpcomingEventNotification;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\Tenant;
use DateInterval;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class SendUpcomingEventNotifications implements ShouldQueue
{
    use Batchable, Queueable;

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

        Tenant::findOrFail($this->tenantKey)->run(function () {
            // Chunk the events to even out the workload
            Event::query()->with('schedule')->chunk(100, function (Collection $events) {
                $events
                    // Reject events that do not have notifications enabled, channels/intervals set or registration is disabled
                    ->reject(fn (Event $event) => ! $event->notifications_enabled
                        || blank($event->notifications_interval)
                        || blank($event->notifications_channels)
                        || ! $event->registration_enabled)

                    // Group by the intervals, so we can each interval one at a time
                    ->groupBy('notifications_interval')

                    // Map app the events to notifications by interval, and then flatten to remove the group by keys
                    ->each(function (Collection $events, $interval) {
                        return $events
                            ->each(function (Event $event) use ($interval) {
                                $start = $event->starts ?? $event->schedule->next_occurrence ?? null;

                                if (blank($start)) {
                                    return;
                                }

                                $sendAt = $start->copy()->subtract(new DateInterval(Str::upper($interval)));

                                if ($sendAt->isBetween(now(), now()->addHours(24))) {
                                    SendUpcomingEventNotification::handle($event, NotificationInterval::from($interval), $sendAt);
                                }
                            });
                    });
            });
        });
    }
}
