<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use App\Actions\Messages\SendMessage;
use App\Models\Message;
use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Throwable;

class SendRecurringMessages implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public int $tenantKey)
    {
        $this->onConnection('central');
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(function () {
            // Find all messages where they are repeating
            Message::query()->with('schedule')->where('repeats', true)->chunk(100, function (Collection $messages) {
                $messages
                    // Filter out any messages we can't send notifications for
                    ->filter(fn (Message $message) => SendMessage::canSendNotification($message))

                    // Iterate over the messages that we can send
                    ->each(function (Message $message) {
                        // Determine when the next message should be sent
                        $occurrence = $message->schedule->next_occurrence;

                        // Check if that time is within the next 24 hours, and if it is, send it. We check
                        // 24 hours because the schedule that runs this job only happens once a day.
                        if ($occurrence->isBetween(now(), now()->addHours(24))) {
                            SendMessage::handle($message, $occurrence);
                        }
                    });
            });
        });
    }
}
