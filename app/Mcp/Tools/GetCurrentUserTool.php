<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\RespondsWithApiResults;
use App\Services\InternalApiService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get_current_user')]
#[Title('Get Current User')]
#[Description('Get the PERSCOM member the connection is signed in as.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class GetCurrentUserTool extends Tool
{
    use RespondsWithApiResults;

    public function handle(Request $request): Response
    {
        $response = InternalApiService::get('me');

        if (! $response->isSuccessful()) {
            return $this->respondWithApiResult($response);
        }

        /** @var array{data?: array<int, array<string, mixed>>} $payload */
        $payload = json_decode((string) $response->getContent(), true);

        return Response::json(['data' => $payload['data'][0] ?? null]);
    }
}
