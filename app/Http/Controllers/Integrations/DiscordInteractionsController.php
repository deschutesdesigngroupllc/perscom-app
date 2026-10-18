<?php

declare(strict_types=1);

namespace App\Http\Controllers\Integrations;

use App\Services\DiscordInteractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscordInteractionsController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(DiscordInteractionService::handle($request->json()->all()));
    }
}
