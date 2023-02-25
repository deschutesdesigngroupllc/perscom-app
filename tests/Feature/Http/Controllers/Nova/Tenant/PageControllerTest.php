<?php

namespace Tests\Feature\Http\Controllers\Nova\Tenant;

use Tests\TestCase;
use Tests\Traits\WithTenant;

class PageControllerTest extends TestCase
{
    use WithTenant;

    public function test_dashboard_page_can_be_reached()
    {
        $this->actingAs($this->user)
             ->get($this->tenant->url.'/dashboards/main')
             ->assertSuccessful();
    }
}
