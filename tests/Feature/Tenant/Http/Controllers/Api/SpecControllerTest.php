<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

class SpecControllerTest extends ApiTestCase
{
    public function test_spec_endpoint_can_be_reached()
    {
        $this->get(route('api.spec'))
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'text/yaml; charset=UTF-8');
    }
}
