<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api;

class SpecControllerTest extends ApiTestCase
{
    public function test_spec_endpoint_can_be_reached()
    {
        $this->get('/spec.yaml')
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'text/yaml; charset=UTF-8');
    }
}
