<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\Rank;
use App\Services\InternalApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Tenant\TenantTestCase;
use Tests\Traits\MakesApiRequests;
use Tests\Traits\WithApiKey;

class InternalApiServiceTest extends TenantTestCase
{
    use MakesApiRequests;
    use WithApiKey;

    public function test_it_forwards_the_callers_token_and_pins_the_current_tenant(): void
    {
        Route::domain(config('api.url'))->get(config('api.version').'/internal-api-echo', fn (Request $request): JsonResponse => response()->json([
            'authorization' => $request->header('Authorization'),
            'tenant' => $request->header('X-Perscom-Id'),
            'query' => $request->query('limit'),
        ]));

        $original = Request::create($this->tenant->url.'/mcp', 'POST', server: ['HTTP_AUTHORIZATION' => 'Bearer caller-token']);
        $this->app->instance('request', $original);

        $response = InternalApiService::get('internal-api-echo', ['limit' => 5]);

        $this->assertSame([
            'authorization' => 'Bearer caller-token',
            'tenant' => (string) $this->tenant->getKey(),
            'query' => '5',
        ], json_decode((string) $response->getContent(), true));

        $this->assertSame($original, request(), 'The original request must be restored after the sub-request.');
    }

    public function test_api_scopes_and_policies_still_apply_to_sub_requests(): void
    {
        $this->withoutApiMiddleware();

        $rank = Rank::factory()->createQuietly();

        $this->app->instance('request', Request::create($this->tenant->url.'/mcp', 'POST', server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$this->apiKey(['view:user']),
        ]));

        $this->assertSame(403, InternalApiService::get('ranks/'.$rank->getKey())->getStatusCode());
    }
}
