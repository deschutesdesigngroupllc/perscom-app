<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Jobs\PurgeApiCache;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;
use Tests\Traits\MakesApiRequests;
use Tests\Traits\WithApiKey;

class CacheControllerTest extends TenantTestCase
{
    use MakesApiRequests;
    use WithApiKey;

    public function test_it_queues_the_purge_api_cache_job()
    {
        $this->withoutApiMiddleware();

        Queue::fake(PurgeApiCache::class);

        $this->withToken($this->apiKey(['clear:cache']))
            ->postJson(route('api.cache', [
                'version' => config('api.version'),
            ]))
            ->assertSuccessful()
            ->assertExactJson([
                'status' => 'okay',
            ]);

        Queue::assertPushed(PurgeApiCache::class, function (PurgeApiCache $job) {
            return $job->tags === "tenant:{$this->tenant->getTenantKey()}";
        });
    }

    public function test_cannot_call_api_with_incorrect_scopes()
    {
        $this->withoutApiMiddleware();

        Queue::fake(PurgeApiCache::class);

        $this->withToken($this->apiKey())
            ->postJson(route('api.cache', [
                'version' => config('api.version'),
            ]))
            ->assertForbidden();
    }
}
