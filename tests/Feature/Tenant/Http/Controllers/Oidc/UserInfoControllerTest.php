<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Oidc;

use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;
use Tests\Traits\MakesApiRequests;
use Tests\Traits\WithApiKey;

class UserInfoControllerTest extends TenantTestCase
{
    use MakesApiRequests;
    use WithApiKey;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createQuietly();
    }

    public function test_userinfo_endpoint_can_be_reached()
    {
        $this->withoutApiMiddleware();

        $this->withToken($this->apiKey(['openid', 'profile', 'email'], $this->user))
            ->get($this->tenant->route('oidc.userinfo'))
            ->assertSuccessful()
            ->assertJson([
                'id' => $this->user->getAuthIdentifier(),
                'name' => $this->user->name,
                'preferred_username' => $this->user->email,
                'profile' => $this->user->url,
                'email' => $this->user->email,
                'email_verified' => $this->user->hasVerifiedEmail(),
                'picture' => $this->user->profile_photo_url,
                'phone_number' => $this->user->phone_number,
                'tenant' => (string) $this->tenant->getTenantKey(),
                'roles' => $this->user->roles->pluck('name')->toArray(),
                'locale' => config('app.locale'),
                'zoneinfo' => 'UTC',
            ]);
    }

    public function test_cannot_reach_userinfo_endpoint_without_openid_scope()
    {
        $this->withoutApiMiddleware();

        $this->withToken($this->apiKey(['profile', 'email'], $this->user))
            ->get($this->tenant->route('oidc.userinfo'))
            ->assertForbidden();
    }

    public function test_userinfo_endpoint_does_not_return_email_scope()
    {
        $this->withoutApiMiddleware();

        $this->withToken($this->apiKey(['profile', 'openid'], $this->user))
            ->get($this->tenant->route('oidc.userinfo'))
            ->assertSuccessful()
            ->assertJson([
                'id' => $this->user->getKey(),
                'name' => $this->user->name,
                'preferred_username' => $this->user->email,
                'profile' => $this->user->url,
                'picture' => $this->user->profile_photo_url,
                'phone_number' => $this->user->phone_number,
                'tenant' => (string) $this->tenant->getTenantKey(),
                'roles' => $this->user->roles->pluck('name')->toArray(),
                'locale' => config('app.locale'),
                'zoneinfo' => 'UTC',
            ])
            ->assertJsonMissing([
                'email' => $this->user->email,
                'email_verified' => $this->user->hasVerifiedEmail(),
            ]);
    }

    public function test_userinfo_endpoint_does_not_return_profile_scope()
    {
        $this->withoutApiMiddleware();

        $this->withToken($this->apiKey(['openid', 'email'], $this->user))
            ->get($this->tenant->route('oidc.userinfo'))
            ->assertSuccessful()
            ->assertJson([
                'id' => $this->user->getKey(),
                'email' => $this->user->email,
                'email_verified' => $this->user->hasVerifiedEmail(),
            ])
            ->assertJsonMissing([
                'name' => $this->user->name,
                'preferred_username' => $this->user->email,
                'profile' => $this->user->url,
                'picture' => $this->user->profile_photo_url,
                'phone_number' => $this->user->phone_number,
                'tenant' => (string) $this->tenant->getTenantKey(),
                'roles' => $this->user->roles->pluck('name')->toArray(),
                'locale' => config('app.locale'),
                'zoneinfo' => 'UTC',
            ]);
    }

    public function test_userinfo_endpoint_only_returns_id_for_openid_scope()
    {
        $this->withoutApiMiddleware();

        $this->withToken($this->apiKey(['openid'], $this->user))
            ->get($this->tenant->route('oidc.userinfo'))
            ->assertSuccessful()
            ->assertJson([
                'id' => $this->user->getKey(),
            ])
            ->assertJsonMissing([
                'name' => $this->user->name,
                'preferred_username' => $this->user->email,
                'profile' => $this->user->url,
                'email' => $this->user->email,
                'email_verified' => $this->user->hasVerifiedEmail(),
                'picture' => $this->user->profile_photo_url,
                'phone_number' => $this->user->phone_number,
                'tenant' => (string) $this->tenant->getTenantKey(),
                'roles' => $this->user->roles->pluck('name')->toArray(),
                'locale' => config('app.locale'),
                'zoneinfo' => 'UTC',
            ]);
    }
}
