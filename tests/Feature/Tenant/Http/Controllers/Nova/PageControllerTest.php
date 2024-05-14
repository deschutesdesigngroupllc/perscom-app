<?php

namespace Tests\Feature\Tenant\Http\Controllers\Nova;

use App\Http\Middleware\Subscribed;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\Feature\Tenant\TenantTestCase;

class PageControllerTest extends TenantTestCase
{
    public function test_dashboard_page_can_be_reached()
    {
        $this->withoutMiddleware([Subscribed::class]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('nova.pages.dashboard.custom', [
                'name' => 'main',
            ]));

        Log::debug('Response', [
            'response' => $response,
        ]);

        $response->assertSuccessful();
    }
}
