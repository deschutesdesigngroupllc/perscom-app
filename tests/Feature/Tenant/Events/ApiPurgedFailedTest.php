<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Events;

use App\Events\ApiPurgedFailed;
use Tests\Feature\Tenant\TenantTestCase;

class ApiPurgedFailedTest extends TenantTestCase
{
    public function test_it_assigns_the_tags_and_event_to_public_properties(): void
    {
        $tags = ['users', 'ranks'];

        $event = new ApiPurgedFailed($tags, 'deleted');

        $this->assertSame($tags, $event->tags);
        $this->assertSame('deleted', $event->event);
    }
}
