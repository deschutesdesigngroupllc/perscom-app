<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Mcp;

use Illuminate\Testing\TestResponse;
use Tests\Feature\Tenant\TenantTestCase;
use Tests\Traits\MakesApiRequests;
use Tests\Traits\WithApiKey;

abstract class McpTestCase extends TenantTestCase
{
    use MakesApiRequests;
    use WithApiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutApiMiddleware();
    }

    /**
     * @param  array<string, mixed>  $params
     */
    protected function mcp(string $method, array $params = [], ?string $token = null): TestResponse
    {
        $request = $this->withHeaders(['Accept' => 'application/json, text/event-stream']);

        if (filled($token)) {
            $request = $request->withToken($token);
        }

        return $request->postJson($this->tenant->url.'/mcp', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => $method,
            'params' => $params,
        ]);
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function callTool(string $tool, array $arguments, string $token): TestResponse
    {
        return $this->mcp('tools/call', [
            'name' => $tool,
            'arguments' => $arguments,
        ], $token);
    }

    /**
     * @return array<string, mixed>
     */
    protected function toolJson(TestResponse $response): array
    {
        $response->assertOk()->assertJsonPath('result.isError', false);

        return json_decode((string) $response->json('result.content.0.text'), true);
    }

    protected function toolError(TestResponse $response): string
    {
        $response->assertOk()->assertJsonPath('result.isError', true);

        return (string) $response->json('result.content.0.text');
    }
}
