<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use Illuminate\Support\Facades\URL;
use Tests\Feature\Tenant\Requests\Traits\MakesApiRequests;
use Tests\Feature\Tenant\TenantTestCase;

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
