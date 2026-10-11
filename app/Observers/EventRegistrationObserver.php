<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\WebhookEvent;
use App\Models\EventRegistration;
use App\Models\Webhook;
use App\Services\EventAttendanceService;
use App\Services\WebhookService;
use App\Traits\DispatchesAutomationEvents;

class EventRegistrationObserver
{
    use DispatchesAutomationEvents;

    public function created(EventRegistration $registration): void
    {
        $this->handleAttendance($registration, created: true);
    }

    public function updated(EventRegistration $registration): void
    {
        if (! $registration->wasChanged('status')) {
            return;
        }

        $this->handleAttendance($registration, created: false);
    }

    protected function handleAttendance(EventRegistration $registration, bool $created): void
    {
        [$trigger, $webhookEvent] = match ($registration->status) {
            EventRegistrationStatus::ATTENDED => [AutomationTrigger::EVENT_REGISTRATION_ATTENDED, WebhookEvent::EVENT_REGISTRATION_ATTENDED],
            EventRegistrationStatus::NO_SHOW => [AutomationTrigger::EVENT_REGISTRATION_NO_SHOW, WebhookEvent::EVENT_REGISTRATION_NO_SHOW],
            default => [null, null],
        };

        if (! $trigger instanceof AutomationTrigger || ! $webhookEvent instanceof WebhookEvent) {
            return;
        }

        Webhook::query()->whereJsonContains('events', [$webhookEvent->value])->each(function (Webhook $webhook) use ($registration, $webhookEvent): void {
            WebhookService::dispatch($webhook, $webhookEvent->value, $registration);
        });

        if ($created) {
            $this->dispatchAutomationCreated($registration, $trigger);
        } else {
            $this->dispatchAutomationUpdated($registration, $trigger);
        }

        EventAttendanceService::createTrainingRecord($registration);
    }
}
