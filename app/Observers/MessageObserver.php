<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\Messages\SendMessage;
use App\Models\Message;
use Throwable;

class MessageObserver
{
    /**
     * @throws Throwable
     */
    public function created(Message $message): void
    {
        if (blank($message->send_at) && ! $message->repeats) {
            SendMessage::handle($message);
        }
    }

    public function updated(Message $message): void
    {
        if ($message->isDirty('repeats') && ! $message->repeats) {
            $message->schedule()->delete();
        }
    }

    public function deleted(Message $message): void
    {
        //
    }

    public function restored(Message $message): void
    {
        //
    }

    public function forceDeleted(Message $message): void
    {
        //
    }
}
