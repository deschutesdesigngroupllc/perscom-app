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
     */
    public function created(AssignmentRecord $assignment): void
    {
        if ($assignment->user) {
            $assignment->user->position_id = $assignment->position?->id;
            $assignment->user->specialty_id = $assignment->specialty?->id;
            $assignment->user->unit_id = $assignment->unit?->id;
            $assignment->user->save();
            $assignment->user->secondary_positions()->sync($assignment->secondary_position_ids);
            $assignment->user->secondary_specialties()->sync($assignment->secondary_specialty_ids);
            $assignment->user->secondary_units()->sync($assignment->secondary_unit_ids);
        }

        Notification::send($assignment->user, new NewAssignmentRecord($assignment));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_CREATED->value, $assignment);
        });
    }

    /**
     * Handle the Assignment "updated" event.
     */
    public function updated(AssignmentRecord $assignment): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_UPDATED->value, $assignment);
        });
    }

    /**
     * Handle the Assignment "deleted" event.
     */
    public function deleted(AssignmentRecord $assignment): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_DELETED->value, $assignment);
        });
    }

    /**
     * Handle the Assignment "restored" event.
     */
    public function restored(AssignmentRecord $assignment): void
    {
        //
    }

    /**
     * Handle the Assignment "force deleted" event.
     */
    public function forceDeleted(AssignmentRecord $assignment): void
    {
        //
    }
}
