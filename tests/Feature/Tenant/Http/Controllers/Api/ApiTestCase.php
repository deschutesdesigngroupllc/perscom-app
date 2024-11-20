<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Jobs\PurgeApiCache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Tests\Feature\Tenant\TenantTestCase;
use Tests\Traits\MakesApiRequests;
use Tests\Traits\WithApiKey;

class ApiTestCase extends TenantTestCase
{
    use MakesApiRequests;
    use WithApiKey;

    protected function setUp(): void
    {
        parent::setUp();

        URL::forceRootUrl(config('api.url').'/'.config('api.version'));

        Queue::fake([PurgeApiCache::class]);

        $this->withoutApiMiddleware();
    }
}
