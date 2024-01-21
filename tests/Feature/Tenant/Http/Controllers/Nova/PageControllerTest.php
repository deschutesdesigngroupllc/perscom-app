<?php

namespace Tests\Feature\Tenant\Http\Controllers\Nova;

use App\Http\Middleware\Subscribed;
use Tests\Feature\Tenant\TenantTestCase;

class PageControllerTest extends TenantTestCase
{
    public function test_dashboard_page_can_be_reached()
    {
        $this->withoutMiddleware([Subscribed::class]);

        $this->actingAs($this->user)
            ->get('/dashboards/main')
            ->assertSuccessful();
    }
}
