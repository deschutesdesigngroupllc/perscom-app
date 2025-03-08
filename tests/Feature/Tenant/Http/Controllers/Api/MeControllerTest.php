<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Models\User;

class MeControllerTest extends ApiTestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createQuietly();
    }

    public function test_me_endpoint_can_be_reached(): void
    {
        $this->withToken($this->apiKey([]))
            ->getJson(route('api.me.index', [
                'version' => config('api.version'),
            ]))
            ->assertSuccessful();
    }
}
