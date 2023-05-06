<?php

namespace Tests\Tenant\Feature\Http\Controllers\Api;

use Illuminate\Support\Facades\URL;
use Tests\Tenant\TenantTestCase;
use Tests\Traits\MakesApiRequests;

class ApiTestCase extends TenantTestCase
{
    use MakesApiRequests;

    protected function setUp(): void
    {
        parent::setUp();

        URL::forceRootUrl(config('app.api_url').'/'.config('app.api_version'));

        $this->withHeader('X-Perscom-Id', $this->tenant->getTenantKey());

        $this->withoutApiMiddleware();
    }
}
