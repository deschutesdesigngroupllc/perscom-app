<?php

namespace Tests\Tenant\Feature\Http\Controllers\Nova;

use Tests\Tenant\TenantTestCase;

class PageControllerTest extends TenantTestCase
{
    public function test_dashboard_page_can_be_reached()
    {
        $this->actingAs($this->user)
             ->get('/dashboards/main')
             ->assertSuccessful();
    }
}
