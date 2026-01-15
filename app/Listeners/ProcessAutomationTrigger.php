<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\AutomationTriggerable;
use App\Services\AutomationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessAutomationTrigger implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'default';

    public function __construct(
        protected AutomationService $automationService,
    ) {
        //
    }

    public function handle(AutomationTriggerable $event): void
    {
        $this->automationService->process($event);
    }
}
