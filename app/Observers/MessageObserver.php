<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\Messages\SendMessage;
use App\Models\Enums\WebhookEvent;
use App\Models\Message;
use App\Models\Webhook;
use App\Services\WebhookService;
use Throwable;

class MessageObserver
{
    /**
     * @throws Throwable
     */
    public function created(Message $message): void
    {
        if (! $message->repeats) {
            SendMessage::handle($message);
        }

        Webhook::query()->whereJsonContains('events', [WebhookEvent::MESSAGE_CREATED->value])->each(function (Webhook $webhook) use ($message): void {
            WebhookService::dispatch($webhook, WebhookEvent::MESSAGE_CREATED->value, $message);
        });
    }

    public function updated(Message $message): void
    {
        if ($message->isDirty('repeats') && ! $message->repeats) {
            $message->schedule()->delete();
        }

        Webhook::query()->whereJsonContains('events', [WebhookEvent::MESSAGE_UPDATED->value])->each(function (Webhook $webhook) use ($message): void {
            WebhookService::dispatch($webhook, WebhookEvent::MESSAGE_UPDATED->value, $message);
        });
    }

    public function deleted(Message $message): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::MESSAGE_DELETED->value])->each(function (Webhook $webhook) use ($message): void {
            WebhookService::dispatch($webhook, WebhookEvent::MESSAGE_DELETED->value, $message);
        });
    }
}
