<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;

class SpecControllerTest extends ApiTestCase
{
    public function test_spec_endpoint_can_be_reached()
    {
        Http::fake(function () {
            return [
                'https://raw.githubusercontent.com/deschutesdesigngroupllc/perscom-docs/master/api-reference/openapi.json' => Http::response(''),
            ];
        });

        $this->get(route('api.spec'))
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'application/json');
    }
}
