<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Traits;

use App\Models\Event;
use Tests\Feature\Tenant\TenantTestCase;

class HasResourceUrlTest extends TenantTestCase
{
    public function test_an_event_without_a_link_falls_back_to_its_resource_url(): void
    {
        $event = Event::factory()->createQuietly(['url' => null]);

        $this->assertStringContainsString(sprintf('/events/%d', $event->getKey()), (string) $event->url);
    }

    public function test_resource_urls_are_generated_without_an_initialized_tenant(): void
    {
        $event = Event::factory()->createQuietly(['url' => null]);
        $fresh = $event->replicate()->setRawAttributes($event->getAttributes(), true);

        tenancy()->end();

        try {
            $url = $fresh->url;
        } finally {
            tenancy()->initialize($this->tenant);
        }

        $this->assertStringEndsWith(sprintf('/events/%d', $event->getKey()), (string) $url);
    }
}
