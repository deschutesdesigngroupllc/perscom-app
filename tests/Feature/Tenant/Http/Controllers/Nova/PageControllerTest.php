<?php

namespace Tests\Feature\Tenant\Http\Controllers\Nova;

use Tests\Feature\Tenant\TenantTestCase;

class PageControllerTest extends TenantTestCase
{
    public function test_dashboard_page_can_be_reached()
    {
        $this->actingAs($this->user)
            ->get('/dashboards/main')
            ->assertSuccessful();
    }
}
