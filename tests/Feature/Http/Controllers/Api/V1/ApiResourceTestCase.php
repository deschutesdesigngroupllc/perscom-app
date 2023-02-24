<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Tests\Feature\Http\Controllers\Api\ApiTestCase;

class ApiResourceTestCase extends ApiTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        FeatureFlag::shouldReceive('isOff')->with('billing')->andReturn(true);
    }
}
