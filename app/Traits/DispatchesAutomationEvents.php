<?php

declare(strict_types=1);

namespace App\Traits;

use App\Events\Automations\AbstractAutomationEvent;
use App\Events\Automations\RecordCreated;
use App\Events\Automations\RecordDeleted;
use App\Events\Automations\RecordUpdated;
use App\Events\Automations\UserCreated;
use App\Events\Automations\UserDeleted;
use App\Events\Automations\UserUpdated;
use App\Models\Enums\AutomationTrigger;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait DispatchesAutomationEvents
{
    protected function dispatchAutomationCreated(Model $model, AutomationTrigger $trigger): void
    {
        if ($model instanceof User) {
            event(new UserCreated($model));
        } else {
            event(new RecordCreated($model, $trigger));
        }
    }

    protected function dispatchAutomationUpdated(Model $model, AutomationTrigger $trigger): void
    {
        $changes = AbstractAutomationEvent::buildChangedAttributes($model);

        if ($model instanceof User) {
            event(new UserUpdated($model, $changes));
        } else {
            event(new RecordUpdated($model, $trigger, $changes));
        }
    }

    protected function dispatchAutomationDeleted(Model $model, AutomationTrigger $trigger): void
    {
        if ($model instanceof User) {
            event(new UserDeleted($model));
        } else {
            event(new RecordDeleted($model, $trigger));
        }
    }
}
