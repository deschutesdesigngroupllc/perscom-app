<?php

namespace Tests\Feature\Tenant\Http\Controllers\Nova;

use App\Http\Middleware\Subscribed;
use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class PageControllerTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([Subscribed::class]);
    }

    public function test_dashboard_page_can_be_reached()
    {
        $this->markTestSkipped('TODO: Fix failing test in CI.');

        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('nova.pages.dashboard.custom', [
                'name' => 'main',
            ]))
            ->assertSuccessful();
    }
}
