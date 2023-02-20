<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Tests\Feature\Http\Controllers\Api\ApiTestCase;
use Tests\Traits\WithTenant;

class ApiResourceTestCase extends ApiTestCase
{
    use WithTenant;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(true);
    }
}
