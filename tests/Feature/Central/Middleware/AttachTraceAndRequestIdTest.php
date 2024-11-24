<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Middleware;

use Illuminate\Support\Facades\Context;
use Tests\Feature\Central\CentralTestCase;

class AttachTraceAndRequestIdTest extends CentralTestCase
{
    public function test_request_and_trace_ids_are_attached_to_response_headers()
    {
        $this->get(route('web.landing.home'))
            ->assertHeader('X-Perscom-Request-Id', Context::get('request_id'))
            ->assertHeader('X-Perscom-Trace-Id', Context::get('trace_id'));
    }
}
