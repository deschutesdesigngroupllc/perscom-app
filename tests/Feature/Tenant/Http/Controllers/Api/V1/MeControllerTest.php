<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\User;
use Laravel\Passport\Passport;
use Tests\Feature\Tenant\Http\Controllers\Api\ApiTestCase;

class MeControllerTest extends ApiTestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_me_endpoint_can_be_reached()
    {
        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'))
            ->assertSuccessful();
    }

    public function test_me_endpoint_cannot_be_reached_without_proper_scope()
    {
        Passport::actingAs($this->user);

        $this->getJson(route('api.me.index'))
            ->assertForbidden();
    }
}
