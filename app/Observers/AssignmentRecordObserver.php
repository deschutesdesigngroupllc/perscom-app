<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AssignmentRecord;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAssignmentRecord;
use App\Services\AssignmentRecordService;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;
use Illuminate\Support\Facades\Notification;

class AssignmentRecordObserver
{
    use DispatchesAutomationEvents;

    public function created(AssignmentRecord $assignment): void
    {
        AssignmentRecordService::process($assignment);

        Notification::send($assignment->user, new NewAssignmentRecord($assignment));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($assignment): void {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_CREATED->value, $assignment);
        });

        $this->dispatchAutomationCreated($assignment, AutomationTrigger::ASSIGNMENT_RECORD_CREATED);
    }

    public function updated(AssignmentRecord $assignment): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($assignment): void {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_UPDATED->value, $assignment);
        });

        $this->dispatchAutomationUpdated($assignment, AutomationTrigger::ASSIGNMENT_RECORD_UPDATED);
    }

    public function deleted(AssignmentRecord $assignment): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($assignment): void {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_DELETED->value, $assignment);
        });

        $this->dispatchAutomationDeleted($assignment, AutomationTrigger::ASSIGNMENT_RECORD_DELETED);
    }
}
