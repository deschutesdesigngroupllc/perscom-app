<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use Illuminate\Support\Facades\URL;
use Tests\Feature\Tenant\TenantTestCase;
use Tests\Traits\MakesApiRequests;

class ApiTestCase extends TenantTestCase
{
    use MakesApiRequests;

    protected function setUp(): void
    {
        parent::setUp();

        URL::forceRootUrl(config('api.url').'/'.config('api.version'));

        $this->withHeader('X-Perscom-Id', (string) $this->tenant->getTenantKey());

        $this->withoutApiMiddleware();
    }
}
