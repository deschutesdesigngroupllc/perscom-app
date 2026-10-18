<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\AiUsage;
use Illuminate\Support\Facades\Auth;
use Laravel\Ai\Events\AgentPrompted;

class RecordAiUsage
{
    public function handle(AgentPrompted $event): void
    {
        if (! tenancy()->initialized) {
            return;
        }

        AiUsage::create([
            'user_id' => Auth::id(),
            'invocation_id' => $event->invocationId,
            'agent' => $event->prompt->agent::class,
            'provider' => $event->response->meta->provider,
            'model' => $event->response->meta->model,
            'input_tokens' => $event->response->usage->inputTokens,
            'output_tokens' => $event->response->usage->outputTokens,
        ]);
    }
}
