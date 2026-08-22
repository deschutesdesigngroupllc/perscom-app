<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Events;

use App\Events\ApiPurgedSuccessful;
use Tests\Feature\Tenant\TenantTestCase;

class ApiPurgedSuccessfulTest extends TenantTestCase
{
    public function test_it_assigns_the_tags_and_event_to_public_properties(): void
    {
        $tags = ['users', 'ranks'];

        $event = new ApiPurgedSuccessful($tags, 'created');

        $this->assertSame($tags, $event->tags);
        $this->assertSame('created', $event->event);
    }
}
