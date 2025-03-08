<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Passport;

use App\Http\Middleware\CheckSubscription;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Laravel\Passport\Database\Factories\ClientFactory;
use Spatie\Url\Url;
use Tests\Feature\Tenant\TenantTestCase;

class AuthorizationControllerTest extends TenantTestCase
{
    public function test_authorization_page_can_be_reached(): void
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $client = ClientFactory::new()->create();

        $this->actingAs($user)
            ->get($this->tenant->route('passport.authorizations.authorize', [
                'response_type' => 'code',
                'client_id' => $client->getKey(),
                'state' => Str::random(),
                'redirect_url' => $client->redirect,
                'scope' => 'view:user',
            ]))
            ->assertInertia(fn (AssertableInertia $page): \Illuminate\Testing\Fluent\AssertableJson => $page
                ->where('client', $client->getKey())
                ->where('description', $client->description)
                ->where('name', $client->name)
                ->where('scopes', [
                    ['id' => 'view:user', 'description' => 'Can view a user'],
                ])
            )
            ->assertSuccessful();
    }

    public function test_authorization_page_forces_login_when_already_logged_in(): void
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $client = ClientFactory::new()->create();

        $this->actingAs($user)
            ->get($this->tenant->route('passport.authorizations.authorize', [
                'response_type' => 'code',
                'client_id' => $client->getKey(),
                'state' => Str::random(),
                'redirect_url' => $client->redirect,
                'scope' => 'view:user',
                'prompt' => 'login',
            ]))
            ->assertRedirect('/login');
    }

    public function test_approve_authorization_returns_redirect_as_query_parameters(): void
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $client = ClientFactory::new()->create();

        $this->actingAs($user)
            ->get($this->tenant->route('passport.authorizations.authorize', [
                'response_type' => 'code',
                'client_id' => $client->getKey(),
                'state' => Str::random(),
                'redirect_url' => $client->redirect,
                'scope' => 'view:user',
            ]));

        $response = $this->withSession([
            'authRequest' => session()->get('authRequest'),
        ])->postJson($this->tenant->route('passport.authorizations.approve'));

        $response->assertRedirect();

        $redirect = Url::fromString($response->headers->get('location'));

        $this->assertTrue(array_key_exists('code', $redirect->getAllQueryParameters()));
        $this->assertTrue(array_key_exists('state', $redirect->getAllQueryParameters()));
    }

    public function test_implicit_grant_returns_token_as_fragment_parameter(): void
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()
            ->createQuietly();

        $client = ClientFactory::new()
            ->create();

        $this->actingAs($user)
            ->get($this->tenant->route('passport.authorizations.authorize', [
                'response_type' => 'token',
                'response_mode' => 'fragment',
                'client_id' => $client->getKey(),
                'state' => Str::random(),
                'redirect_url' => $client->redirect,
                'scope' => 'view:user',
            ]));

        $response = $this->withSession([
            'authRequest' => session()->get('authRequest'),
        ])
            ->postJson($this->tenant->route('passport.authorizations.approve'));

        $response->assertRedirect();

        $redirect = Url::fromString($response->headers->get('location'));

        $this->assertTrue(Str::contains($redirect->getFragment(), 'access_token'));
        $this->assertTrue(Str::contains($redirect->getFragment(), 'token_type'));
        $this->assertTrue(Str::contains($redirect->getFragment(), 'expires_in'));
    }
}
