<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAssignmentRecord;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class AssignmentRecordObserver
{
    public function created(AssignmentRecord $assignment): void
    {
        if ($assignment->user) {
            if ($assignment->type === AssignmentRecordType::PRIMARY) {
                if ($assignment->isDirty('position_id')) {
                    $assignment->user->position_id = optional($assignment->position)->id;
                }
                if ($assignment->isDirty('specialty_id')) {
                    $assignment->user->specialty_id = optional($assignment->specialty)->id;
                }
                if ($assignment->isDirty('unit_id')) {
                    $assignment->user->unit_id = optional($assignment->unit)->id;
                }
                if ($assignment->isDirty('status_id')) {
                    if (is_null($assignment->status_id)) {
                        $assignment->user->status_id = null;
                    } else {
                        $assignment->user->statuses()->attach($assignment->status);
                    }
                }
            }

            $assignment->user->save();
        }

        Notification::send($assignment->user, new NewAssignmentRecord($assignment));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_CREATED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_CREATED->value, $assignment);
        });
    }

    public function updated(AssignmentRecord $assignment): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_UPDATED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_UPDATED->value, $assignment);
        });
    }

    public function deleted(AssignmentRecord $assignment): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::ASSIGNMENT_RECORD_DELETED->value])->each(function (Webhook $webhook) use ($assignment) {
            WebhookService::dispatch($webhook, WebhookEvent::ASSIGNMENT_RECORD_DELETED->value, $assignment);
        });
    }
}
