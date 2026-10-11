<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\TrainingRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewTrainingRecord;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;
use Illuminate\Support\Facades\Notification;

class TrainingRecordObserver
{
    use DispatchesAutomationEvents;

    public function created(TrainingRecord $training): void
    {
        Notification::send($training->user, new NewTrainingRecord($training));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::TRAINING_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($training): void {
            WebhookService::dispatch($webhook, WebhookEvent::TRAINING_RECORD_CREATED->value, $training);
        });

        $this->dispatchAutomationCreated($training, AutomationTrigger::TRAINING_RECORD_CREATED);
    }

    public function updated(TrainingRecord $training): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::TRAINING_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($training): void {
            WebhookService::dispatch($webhook, WebhookEvent::TRAINING_RECORD_UPDATED->value, $training);
        });

        $this->dispatchAutomationUpdated($training, AutomationTrigger::TRAINING_RECORD_UPDATED);
    }

    public function deleted(TrainingRecord $training): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::TRAINING_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($training): void {
            WebhookService::dispatch($webhook, WebhookEvent::TRAINING_RECORD_DELETED->value, $training);
        });

        $this->dispatchAutomationDeleted($training, AutomationTrigger::TRAINING_RECORD_DELETED);
    }
}
