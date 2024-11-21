<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Passport;

use App\Http\Middleware\CheckSubscription;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Laravel\Passport\Database\Factories\ClientFactory;
use Tests\Feature\Tenant\TenantTestCase;

class AuthorizationControllerTest extends TenantTestCase
{
    public function test_authorization_page_can_be_reached()
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $client = ClientFactory::new()->create(['user_id' => $user->getKey(), 'description' => $this->faker->sentence]);

        $this->actingAs($user)
            ->get($this->tenant->route('passport.authorizations.authorize', [
                'response_type' => 'code',
                'client_id' => $client->getKey(),
                'state' => Str::random(),
                'redirect_url' => $client->redirect,
                'scope' => 'view:user',
            ]))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('client', $client->getKey())
                ->where('description', $client->description)
                ->where('name', $client->name)
                ->where('scopes', [
                    ['id' => 'view:user', 'description' => 'Can view a user'],
                ])
            )
            ->assertSuccessful();
    }

    public function test_authorization_page_forces_login_when_already_logged_in()
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $client = ClientFactory::new()->create(['user_id' => $user->getKey(), 'description' => $this->faker->sentence]);

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
}
