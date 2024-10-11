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

    public function __construct(protected int $tenantKey)
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
            Message::query()->where('repeats', true)->chunk(100, function (Collection $messages) {
                $messages->reject(function (Message $message) {
                    return blank($message->schedule)
                        || $message->schedule->has_passed
                        || blank($message->schedule->next_occurrence);
                })->each(function (Message $message) {
                    if (blank($message->schedule)) {
                        return;
                    }

                    $occurrence = $message->schedule->next_occurrence;

                    if ($occurrence && $occurrence->isSameMinute(now())) {
                        SendMessage::handle($message);
                    }
                });
            });
        });
    }
}
