<?php

declare(strict_types=1);

namespace App\Features;

class AiAssistant
{
    /**
     * AI features are available to every organization once a provider key is configured.
     */
    public function resolve(string|int|null $tenantKey): bool
    {
        return filled(config('ai.providers.'.config('ai.default').'.key'));
    }
}
