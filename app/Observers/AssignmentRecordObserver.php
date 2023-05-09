<?php

namespace App\Observers;

use App\Models\AssignmentRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAssignmentRecord;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class AssignmentRecordObserver
{
    /**
     * Handle the Assignment "created" event.
     *
     * @return void
     */
    public function created(AssignmentRecord $assignment)
    {
        Notification::send($assignment->user, new NewAssignmentRecord($assignment));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_CREATED->value, $assignment);
        });
    }

    /**
     * Handle the Assignment "updated" event.
     *
     * @return void
     */
    public function updated(AssignmentRecord $assignment)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_UPDATED->value, $assignment);
        });
    }

    /**
     * Handle the Assignment "deleted" event.
     *
     * @return void
     */
    public function deleted(AssignmentRecord $assignment)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_DELETED->value, $assignment);
        });
    }

    /**
     * Handle the Assignment "restored" event.
     *
     * @return void
     */
    public function restored(AssignmentRecord $assignment)
    {
        //
    }

    /**
     * Handle the Assignment "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(AssignmentRecord $assignment)
    {
        //
    }
}
